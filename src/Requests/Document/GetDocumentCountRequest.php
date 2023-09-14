<?php

namespace CodebarAg\DocuWare\Requests\Document;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToGetDocumentCount;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetDocumentCountRequest extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $dialogId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Query/CountExpression';
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
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $content = $response->throw()->json();
        throw_unless(Arr::has($content, 'Group'), UnableToGetDocumentCount::noCount());

        $group = Arr::get($content, 'Group');
        throw_unless(Arr::has($group, '0'), UnableToGetDocumentCount::noGroupKeyIndexZero());
        $group = Arr::get($group, '0');

        throw_unless(Arr::has($group, 'Count'), UnableToGetDocumentCount::noCount());

        return Arr::get($group, 'Count');
    }
}
