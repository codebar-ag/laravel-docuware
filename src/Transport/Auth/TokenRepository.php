<?php

namespace CodebarAg\DocuWare\Transport\Auth;

use Closure;
use CodebarAg\DocuWare\Config\InstanceConfig;
use CodebarAg\DocuWare\Data\Authentication\RequestTokenData;

/**
 * Stores and resolves OAuth access tokens per instance.
 *
 * @internal
 */
interface TokenRepository
{
    /**
     * Return a valid access token for the instance, fetching (and caching) a new one via
     * `$fetch` on a cache miss/expiry. Implementations must guard the refresh against
     * concurrent stampede.
     *
     * @param  Closure(): RequestTokenData  $fetch
     */
    public function accessToken(InstanceConfig $config, Closure $fetch): string;

    /**
     * Drop the cached token for the instance (e.g. after a 401), forcing a refresh.
     */
    public function forget(InstanceConfig $config): void;
}
