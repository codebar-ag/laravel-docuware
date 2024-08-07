<?php

namespace CodebarAg\DocuWare\Requests\Documents\Download;

use CodebarAg\DocuWare\Responses\Documents\Download\DownloadThumbnailResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class DownloadThumbnail extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $sectionId,
        protected readonly int $page = 0,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Rendering/'.$this->sectionId.'/Thumbnail';
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('laravel-docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('laravel-docuware.configurations.cache.lifetime_in_seconds', 3600);
    }

    public function defaultQuery(): array
    {
        return [
            'page' => $this->page,
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return DownloadThumbnailResponse::fromResponse($response);
    }
}
