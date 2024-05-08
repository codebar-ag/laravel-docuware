<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\Dialogs;

use CodebarAg\DocuWare\Enums\DialogType;
use CodebarAg\DocuWare\Responses\FileCabinets\Dialogs\GetAllDialogsResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetDialogsOfASpecificType extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly DialogType $dialogType,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Dialogs';
    }

    public function defaultQuery(): array
    {
        return [
            'DialogType' => $this->dialogType->value,
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

    public function createDtoFromResponse(Response $response): mixed
    {
        return GetAllDialogsResponse::fromResponse($response);
    }
}
