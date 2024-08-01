<?php

namespace CodebarAg\DocuWare\Requests\Authentication\OAuth;

use CodebarAg\DocuWare\DTO\Authentication\OAuth\ResponsibleIdentityService;
use CodebarAg\DocuWare\Responses\Authentication\OAuth\GetResponsibleIdentityServiceResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Http\SoloRequest;

class GetResponsibleIdentityService extends SoloRequest implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected ?string $url = null
    ) {}

    public function resolveEndpoint(): string
    {
        $url = $this->url ?? config('laravel-docuware.credentials.url');
        $base = $url.'/DocuWare/Platform';

        return $base.'/Home/IdentityServiceInfo';
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

    public function createDtoFromResponse(Response $response): ResponsibleIdentityService
    {
        return GetResponsibleIdentityServiceResponse::fromResponse($response);
    }
}
