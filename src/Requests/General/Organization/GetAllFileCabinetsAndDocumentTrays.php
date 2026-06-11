<?php

namespace CodebarAg\DocuWare\Requests\General\Organization;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\General\Organization\FileCabinet;
use CodebarAg\DocuWare\Responses\General\Organization\GetAllFileCabinetsAndDocumentTraysResponse;
use Illuminate\Support\Collection;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetAllFileCabinetsAndDocumentTrays extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    /**
     * (Optional) The ID of the specified organization. This is only needed if you are connecting to an on premises system with an Enterprise server that has more than one organization.
     */
    public function __construct(
        public ?string $organizationId = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets';
    }

    protected function defaultQuery(): array
    {
        return [
            'OrgId' => $this->organizationId,
        ];
    }

    /**
     * @return Collection<int, FileCabinet>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return GetAllFileCabinetsAndDocumentTraysResponse::fromResponse($response);
    }
}
