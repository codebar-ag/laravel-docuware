<?php

namespace CodebarAg\DocuWare\Requests\Organization;

use CodebarAg\DocuWare\DTO\Organization;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Responses\Organization\GetOrganizationResponse;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;

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
        return new LaravelCacheDriver(Cache::store(config('docuware.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('docuware.cache.expiry_in_seconds', 3600);
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return GetOrganizationResponse::fromResponse($response);
    }
}
