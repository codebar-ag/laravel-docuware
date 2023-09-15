<?php

namespace CodebarAg\DocuWare\Requests\Document;

use CodebarAg\DocuWare\Exceptions\UnableToDownloadDocuments;
use CodebarAg\DocuWare\Responses\Document\GetDocumentsDownloadResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetDocumentsDownloadRequest extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    protected readonly string $documentId;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected array $documentIds,
    ) {
        throw_if(
            count($documentIds) < 2,
            UnableToDownloadDocuments::selectAtLeastTwoDocuments(),
        );

        $this->documentId = Arr::get($documentIds, 0);
        $this->documentIds = array_slice($documentIds, 1);
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/FileDownload';
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('docuware.configurations.cache.lifetime_in_seconds', 3600);
    }

    public function defaultQuery(): array
    {
        return [
            'keepAnnotations' => 'false',
            'append' => implode(',', $this->documentIds),
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return GetDocumentsDownloadResponse::fromResponse($response);
    }
}
