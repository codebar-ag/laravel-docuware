<?php

namespace CodebarAg\DocuWare\Requests\Search;

use CodebarAg\DocuWare\Responses\Search\GetSearchResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Body\HasBody;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GetSearchRequest extends Request implements Cacheable, HasBody
{
    use HasCaching;
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly ?string $fileCabinetId,
        protected readonly ?string $dialogId = null,
        protected readonly array $additionalFileCabinetIds = [],
        protected readonly int $page = 1,
        protected readonly int $perPage = 50,
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
        return new LaravelCacheDriver(Cache::store(config('docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('docuware.configurations.cache.lifetime_in_seconds', 3600);
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
            'Count' => $this->perPage,
            'Start' => ($this->page - 1) * $this->perPage,
            'Condition' => $this->condition,
            'AdditionalCabinets' => $this->additionalFileCabinetIds,
            'SortOrder' => [
                [
                    'Field' => $this->orderField,
                    'Direction' => $this->orderDirection,
                ],
            ],
            'Operation' => config('docuware.configurations.search.operation', 'And'),
            'ForceRefresh' => config('docuware.configurations.search.force_refresh', true),
            'IncludeSuggestions' => config('docuware.configurations.search.include_suggestions', false),
            'AdditionalResultFields' => config('docuware.configurations.search.additional_result_fields', []),
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return GetSearchResponse::fromResponse($response, $this->page, $this->perPage);
    }
}
