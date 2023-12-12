<?php

namespace CodebarAg\DocuWare\Requests\Organization;

use CodebarAg\DocuWare\DTO\Organization;
use CodebarAg\DocuWare\Responses\Organization\GetOrganizationResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetOrganizationRequest extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public string $organizationId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/Organizations/'.$this->organizationId;
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('laravel-docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('laravel-docuware.configurations.cache.lifetime_in_seconds', 3600);
    }

    public function createDtoFromResponse(Response $response): Organization
    {
        return GetOrganizationResponse::fromResponse($response);
    }
}
