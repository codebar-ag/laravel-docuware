<?php

namespace CodebarAg\DocuWare\Requests\Search;

use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\Responses\Search\GetSearchResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Saloon\Traits\Body\HasJsonBody;

class GetSearchRequest extends Request implements Cacheable, HasBody, Paginatable
{
    use HasCaching;
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly ?string $fileCabinetId,
        protected readonly ?string $dialogId = null,
        protected readonly array $additionalFileCabinetIds = [],
        protected readonly ?string $searchTerm = null,
        protected readonly string $orderField = 'DWSTOREDATETIME',
        protected readonly string $orderDirection = 'asc',
        protected readonly array $condition = [],
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Query/DialogExpression';
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
        $defultQuery = [];

        if ($this->dialogId) {
            $defultQuery['dialogId'] = $this->dialogId;
        }

        return $defultQuery;
    }

    public function defaultBody(): array
    {
        return [
            'Condition' => $this->condition,
            'AdditionalCabinets' => $this->additionalFileCabinetIds,
            'SortOrder' => [
                [
                    'Field' => $this->orderField,
                    'Direction' => $this->orderDirection,
                ],
            ],
            'Operation' => config('laravel-docuware.configurations.search.operation', 'And'),
            'ForceRefresh' => config('laravel-docuware.configurations.search.force_refresh', true),
            'IncludeSuggestions' => config('laravel-docuware.configurations.search.include_suggestions', false),
            'AdditionalResultFields' => config('laravel-docuware.configurations.search.additional_result_fields', []),
        ];
    }

    public function createDtoFromResponse(Response $response): Collection
    {
//                return GetSearchResponse::fromResponse($response, $this->page, $this->perPage);
        return collect(Arr::get($response->json(), 'Items'))->map(function (array $document) {
            return Document::fromJson($document);
        });
    }
}
