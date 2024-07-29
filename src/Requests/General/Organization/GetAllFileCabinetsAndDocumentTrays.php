<?php

namespace CodebarAg\DocuWare\Requests\General\Organization;

use CodebarAg\DocuWare\Responses\General\Organization\GetAllFileCabinetsAndDocumentTraysResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetAllFileCabinetsAndDocumentTrays extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    /**
     * (Optional) The ID of the specified organization. This is only needed if you are connecting to an on premises system with an Enterprise server that has more than one organization.
     */
    public function __construct(
        public ?string $organizationId = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets';
    }

    protected function defaultQuery(): array
    {
        return [
            'OrgId' => $this->organizationId,
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

    public function createDtoFromResponse(Response $response): Collection|Enumerable
    {
        return GetAllFileCabinetsAndDocumentTraysResponse::fromResponse($response);
    }
}
