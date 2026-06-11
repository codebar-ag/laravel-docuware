<?php

namespace CodebarAg\DocuWare\Requests\General\Organization;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;

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
}
