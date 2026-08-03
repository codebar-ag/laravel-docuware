<?php

namespace CodebarAg\DocuWare\Transport;

use CodebarAg\DocuWare\Config\InstanceConfig;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Security\Redactor;
use CodebarAg\DocuWare\Transport\Auth\OAuthTokenFetcher;
use CodebarAg\DocuWare\Transport\Auth\TokenRepository;
use Illuminate\Support\Facades\Cache;
use Saloon\Enums\Method;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Limit;
use Saloon\RateLimitPlugin\Stores\LaravelCacheStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;

/**
 * The native DocuWare transport: one connector per instance (one Guzzle handler → connection
 * reuse), driven by a typed {@see InstanceConfig}. Auth is delegated to a {@see TokenRepository}
 * (encrypted, locked refresh); transient failures are retried with exponential backoff honoring
 * `Retry-After`; every response emits a redacted {@see ResponseReceived} event.
 *
 * @internal Consumers use the resource gateways, never this class.
 */
class DocuWareConnector extends Connector
{
    use HasRateLimits;

    public function __construct(
        public readonly InstanceConfig $instanceConfig,
        protected readonly TokenRepository $tokens,
        protected readonly OAuthTokenFetcher $fetcher,
    ) {
        $this->configureRetries();
        $this->registerResponseEvent();

        // Client-side throttling is opt-in per instance (config `rate_limit.enabled`); when off,
        // the only rate handling is the server-side 429 back-off in handleRetry().
        $this->useRateLimitPlugin($this->rateLimitConfig()['enabled']);
    }

    public function resolveBaseUrl(): string
    {
        $base = rtrim($this->instanceConfig->url, '/');
        $platform = trim((string) config('laravel-docuware.platform_path', 'DocuWare/Platform'), '/');

        return $base.'/'.$platform;
    }

    /**
     * @return array<string, string>
     */
    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultConfig(): array
    {
        return [
            'timeout' => $this->instanceConfig->requestTimeoutInSeconds,
        ];
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator(
            $this->tokens->accessToken($this->instanceConfig, fn () => $this->fetcher->fetch($this->instanceConfig)),
        );
    }

    /**
     * Retry transient failures only. Idempotent verbs (GET/HEAD/PUT/DELETE/OPTIONS) are retried
     * on connection errors and 5xx; POST is retried only on 429 (rate-limited, not processed).
     */
    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        $idempotent = in_array($request->getMethod(), [
            Method::GET, Method::HEAD, Method::PUT, Method::DELETE, Method::OPTIONS,
        ], true);

        if ($exception instanceof FatalRequestException) {
            return $idempotent;
        }

        $response = $exception->getResponse();
        $status = $response->status();

        if ($status === 429) {
            $this->applyRetryAfter($response);

            return true;
        }

        if ($status >= 500) {
            return $idempotent;
        }

        return false;
    }

    private function configureRetries(): void
    {
        /** @var array<string, mixed> $retry */
        $retry = (array) config('laravel-docuware.retry', []);

        if (($retry['enabled'] ?? true) === false) {
            return;
        }

        $this->tries = (int) ($retry['times'] ?? 3);
        $this->retryInterval = (int) ($retry['base_interval_ms'] ?? 250);
        $this->useExponentialBackoff = true;
        // Return the last (failed) response instead of throwing Saloon's exception — the
        // resource layer's ResponseValidator owns the unified DocuWare error model.
        $this->throwOnMaxTries = false;
    }

    private function applyRetryAfter(Response $response): void
    {
        $retryAfter = $response->header('Retry-After');

        if (is_numeric($retryAfter)) {
            $max = (int) config('laravel-docuware.retry.max_interval_ms', 10000);
            $this->retryInterval = min($max, (int) ((float) $retryAfter * 1000));
        }
    }

    private function registerResponseEvent(): void
    {
        $this->middleware()->onResponse(function (Response $response): void {
            $redactor = new Redactor;
            $psr = $response->getPsrRequest();

            $captureBodies = (bool) config('laravel-docuware.debug.capture_bodies', false);

            ResponseReceived::dispatch(
                $this->instanceConfig->name,
                $psr->getMethod(),
                (string) $psr->getUri()->withQuery(''),
                $response->status(),
                null,
                $this->requestId($response),
                $redactor->redact($response->headers()->all()),
                $captureBodies ? $redactor->string((string) $response->body()) : null,
            );
        });
    }

    private function requestId(Response $response): ?string
    {
        foreach (['X-Request-Id', 'Request-Id', 'request-context'] as $header) {
            $value = $response->header($header);
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * One limit, named by the instance's stable identifier so tenants never share a bucket.
     *
     * @return array<int, Limit>
     */
    protected function resolveLimits(): array
    {
        $config = $this->rateLimitConfig();

        return [
            Limit::allow($config['allow'])
                ->everySeconds($config['per_seconds'])
                ->name('docuware:'.$this->instanceConfig->identifier()),
        ];
    }

    protected function resolveRateLimitStore(): RateLimitStore
    {
        return new LaravelCacheStore(Cache::store($this->instanceConfig->cacheDriver));
    }

    /**
     * Resolve the instance's rate-limit settings: the per-instance `instances.{name}.rate_limit`
     * block overrides the global `rate_limit` defaults.
     *
     * @return array{enabled: bool, allow: int, per_seconds: int}
     */
    private function rateLimitConfig(): array
    {
        /** @var array<string, mixed> $global */
        $global = (array) config('laravel-docuware.rate_limit', []);
        /** @var array<string, mixed> $perInstance */
        $perInstance = (array) config('laravel-docuware.instances.'.$this->instanceConfig->name.'.rate_limit', []);

        $merged = array_merge($global, $perInstance);

        return [
            'enabled' => (bool) ($merged['enabled'] ?? false),
            'allow' => max(1, (int) ($merged['allow'] ?? 60)),
            'per_seconds' => max(1, (int) ($merged['per_seconds'] ?? 60)),
        ];
    }
}
