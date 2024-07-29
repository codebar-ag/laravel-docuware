<?php

namespace CodebarAg\DocuWare\Responses\Authentication\OAuth;

use CodebarAg\DocuWare\DTO\Authentication\OAuth\ResponsibleIdentityService;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class GetResponsibleIdentityServiceResponse
{
    public static function fromResponse(Response $response): ResponsibleIdentityService
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return ResponsibleIdentityService::make($response->json());
    }
}
