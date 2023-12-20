<?php

namespace CodebarAg\DocuWare\Requests\Document;

use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\Responses\Document\GetDocumentsResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetDocumentsRequest extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly int $page = 1,
        protected readonly int $perPage = 50,
    ) {
    }

    public function defaultQuery(): array
    {
        return [
            'count' => $this->perPage,
            'start' => ($this->page - 1) * $this->perPage,
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents';
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('laravel-docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('laravel-docuware.configurations.cache.lifetime_in_seconds', 3600);
    }

    public function createDtoFromResponse(Response $response): DocumentPaginator
    {
        return GetDocumentsResponse::fromResponse($response, $this->page, $this->perPage);
    }
}
