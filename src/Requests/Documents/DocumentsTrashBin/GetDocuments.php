<?php

namespace CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin;

use CodebarAg\DocuWare\DTO\Documents\TrashDocumentPaginator;
use CodebarAg\DocuWare\Responses\Search\GetTrashSearchResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class GetDocuments extends Request implements Cacheable, HasBody
{
    use HasCaching;
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly int $page = 1,
        protected readonly int $perPage = 50,
        protected readonly ?string $searchTerm = null,
        protected readonly ?string $orderField = null,
        protected readonly string $orderDirection = 'desc',
        protected readonly array $condition = [],
        protected readonly ?bool $forceRefresh = true,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/TrashBin/Query';
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('laravel-docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('laravel-docuware.configurations.cache.lifetime_in_seconds', 3600);
    }

    public function defaultBody(): array
    {
        return [
            'Count' => $this->perPage,
            'Start' => ($this->page - 1) * $this->perPage,
            'Condition' => $this->condition,
            'SortOrder' => $this->orderField ? [
                [
                    'Field' => $this->orderField,
                    'Direction' => $this->orderDirection,
                ],
            ] : null,
            'Operation' => config('laravel-docuware.configurations.search.operation', 'And'),
            'ForceRefresh' => config('laravel-docuware.configurations.search.force_refresh', true),
            'IncludeSuggestions' => config('laravel-docuware.configurations.search.include_suggestions', false),
            'AdditionalResultFields' => config('laravel-docuware.configurations.search.additional_result_fields', []),
        ];
    }

    public function createDtoFromResponse(Response $response): TrashDocumentPaginator
    {
        return GetTrashSearchResponse::fromResponse($response, $this->page, $this->perPage);
    }
}
