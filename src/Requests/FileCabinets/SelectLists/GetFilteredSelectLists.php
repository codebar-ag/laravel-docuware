<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\SelectLists;

use CodebarAg\DocuWare\Responses\FileCabinets\SelectLists\GetSelectListsResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class GetFilteredSelectLists extends Request implements Cacheable, HasBody
{
    use HasCaching;
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<string, mixed>  $dialogExpression  Postman-shaped `DialogExpression` object (Operation, Condition, …).
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $dialogId,
        protected readonly string $fieldName,
        protected readonly array $dialogExpression,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Query/SelectListExpression';
    }

    public function defaultQuery(): array
    {
        return [
            'DialogId' => $this->dialogId,
            'FieldName' => $this->fieldName,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'ValuePrefix' => '',
            'Limit' => 100,
            'Typed' => true,
            'ExcludeExternal' => true,
            'DialogExpression' => $this->dialogExpression,
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
        return GetSelectListsResponse::fromResponse($response);
    }
}
