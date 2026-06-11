<?php

namespace CodebarAg\DocuWare\Requests\Fields;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\Documents\Field;
use CodebarAg\DocuWare\Responses\Fields\GetFieldsResponse;
use Illuminate\Support\Collection;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetFieldsRequest extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId;
    }

    /**
     * @return Collection<int, Field>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return GetFieldsResponse::fromResponse($response);
    }
}
