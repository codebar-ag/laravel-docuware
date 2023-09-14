<?php

namespace CodebarAg\DocuWare\Requests;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetSelectListRequest extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $dialogId,
        protected readonly string $fieldName,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Query/SelectListExpression';
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('cache.default')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('docuware.cache.expiry_in_seconds', 3600);
    }

    public function defaultQuery(): array
    {
        return [
            'dialogId' => $this->dialogId,
            'fieldName' => $this->fieldName,
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->json('Value');
    }
}
