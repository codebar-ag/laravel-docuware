<?php

namespace CodebarAg\DocuWare\Requests\Fields;

use CodebarAg\DocuWare\DTO\Documents\Field;
use CodebarAg\DocuWare\Responses\Fields\GetFieldsResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetFieldsRequest extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId;
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
     * @return Collection<int, Field>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return GetFieldsResponse::fromResponse($response);
    }
}
