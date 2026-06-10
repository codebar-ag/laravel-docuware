<?php

namespace CodebarAg\DocuWare\Requests\General\Organization;

use CodebarAg\DocuWare\DTO\General\Organization\Organization;
use CodebarAg\DocuWare\Responses\General\Organization\GetOrganizationResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetOrganization extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/Organizations';
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('laravel-docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('laravel-docuware.configurations.cache.lifetime_in_seconds', 3600);
    }

    /**
     * @return Collection<int, Organization>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return GetOrganizationResponse::fromResponse($response);
    }
}
