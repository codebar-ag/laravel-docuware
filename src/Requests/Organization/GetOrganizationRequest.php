<?php

namespace CodebarAg\DocuWare\Requests\Organization;

use CodebarAg\DocuWare\DTO\Organization;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetOrganizationRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public string $organizationId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/Organizations/'.$this->organizationId;
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $organization = $response->throw()->json();

        return Organization::fromJson($organization);
    }
}
