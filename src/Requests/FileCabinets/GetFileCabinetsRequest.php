<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets;

use CodebarAg\DocuWare\DTO\FileCabinet;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Responses\FileCabinets\GetFileCabinetsResponse;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetFileCabinetsRequest extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/FileCabinets';
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
        return GetFileCabinetsResponse::fromResponse($response);
    }
}
