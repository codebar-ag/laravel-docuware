<?php

namespace CodebarAg\DocuWare\Responses\Organization;

use CodebarAg\DocuWare\DTO\Organization;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Contracts\Response;

final class GetOrganizationResponse
{
    public static function fromResponse(Response $response): Organization
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $organization = $response->throw()->json();

        return Organization::fromJson($organization);
    }
}
