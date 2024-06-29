<?php

namespace CodebarAg\DocuWare\Requests\Documents\Sections;

use CodebarAg\DocuWare\DTO\Section;
use CodebarAg\DocuWare\Responses\Documents\Sections\GetASecificSectionResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetASpecificSection extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $sectionId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Sections/'.$this->sectionId;
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('laravel-docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('laravel-docuware.configurations.cache.lifetime_in_seconds', 3600);
    }

    public function createDtoFromResponse(Response $response): Section
    {
        return GetASecificSectionResponse::fromResponse($response);
    }
}
