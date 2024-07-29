<?php

namespace CodebarAg\DocuWare\Requests\Authentication\OAuth;

use CodebarAg\DocuWare\DTO\Authentication\OAuth\IdentityServiceConfiguration;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Http\SoloRequest;

class GetIdentityServiceConfiguration extends SoloRequest implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public string $identityServiceUrl,
    ) {}

    public function resolveEndpoint(): string
    {
        return $this->identityServiceUrl.'/.well-known/openid-configuration';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('laravel-docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('laravel-docuware.configurations.cache.lifetime_in_seconds', 3600);
    }

    public function createDtoFromResponse(Response $response): IdentityServiceConfiguration
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return IdentityServiceConfiguration::make($response->json());
    }
}
