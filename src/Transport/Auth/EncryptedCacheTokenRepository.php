<?php

namespace CodebarAg\DocuWare\Transport\Auth;

use Closure;
use CodebarAg\DocuWare\Config\InstanceConfig;
use CodebarAg\DocuWare\Data\Authentication\RequestTokenData;
use CodebarAg\DocuWare\Events\TokenRefreshed;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Throwable;

/**
 * Default {@see TokenRepository}: tokens are encrypted with Laravel's `Crypt` and stored in a
 * per-instance-namespaced cache entry, with a cache lock around the refresh so concurrent
 * requests don't stampede the token endpoint.
 *
 * @internal
 */
final class EncryptedCacheTokenRepository implements TokenRepository
{
    /**
     * Seconds subtracted from the token lifetime so a token is refreshed slightly early.
     */
    private const EXPIRY_SKEW_SECONDS = 60;

    public function accessToken(InstanceConfig $config, Closure $fetch): string
    {
        $cache = Cache::store($config->cacheDriver);
        $key = $this->key($config);

        $cached = $this->read($cache, $key);
        if ($cached !== null) {
            return $cached;
        }

        // Re-check inside the critical section: another request may have refreshed meanwhile.
        $refresh = function () use ($cache, $key, $config, $fetch): string {
            $cached = $this->read($cache, $key);
            if ($cached !== null) {
                return $cached;
            }

            $token = $fetch();

            $cache->put($key, Crypt::encrypt($token), max(1, $token->expiresIn - self::EXPIRY_SKEW_SECONDS));

            TokenRefreshed::dispatch($config->name, $config->url);

            return $token->accessToken;
        };

        // Guard the refresh with a cache lock to prevent a token stampede under concurrency.
        // Stores that don't support locks (no LockProvider) fall back to an unguarded refresh.
        $store = $cache->getStore();
        if ($store instanceof LockProvider) {
            return $store->lock($key.':refresh', 15)->block(10, $refresh);
        }

        return $refresh();
    }

    public function forget(InstanceConfig $config): void
    {
        Cache::store($config->cacheDriver)->forget($this->key($config));
    }

    private function read(CacheRepository $cache, string $key): ?string
    {
        if (! $cache->has($key)) {
            return null;
        }

        try {
            $token = Crypt::decrypt($cache->get($key));
        } catch (Throwable) {
            $cache->forget($key);

            return null;
        }

        return $token instanceof RequestTokenData ? $token->accessToken : null;
    }

    private function key(InstanceConfig $config): string
    {
        return 'docuware.oauth.'.$config->identifier();
    }
}
