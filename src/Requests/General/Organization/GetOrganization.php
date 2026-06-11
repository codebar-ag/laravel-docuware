<?php

namespace CodebarAg\DocuWare\Requests\General\Organization;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\General\Organization\Organization;
use CodebarAg\DocuWare\Responses\General\Organization\GetOrganizationResponse;
use Illuminate\Support\Collection;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetOrganization extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/Organizations';
    }

    /**
     * @return Collection<int, Organization>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return GetOrganizationResponse::fromResponse($response);
    }
}
