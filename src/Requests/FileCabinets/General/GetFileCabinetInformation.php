<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\General;

use CodebarAg\DocuWare\DTO\FileCabinets\General\FileCabinetInformation;
use CodebarAg\DocuWare\Responses\FileCabinets\General\GetFileCabinetInformationResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetFileCabinetInformation extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $fileCabinetId,
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

    public function createDtoFromResponse(Response $response): FileCabinetInformation
    {
        return GetFileCabinetInformationResponse::fromResponse($response);
    }
}
