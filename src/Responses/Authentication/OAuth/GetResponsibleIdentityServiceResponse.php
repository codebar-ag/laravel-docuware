<?php

namespace CodebarAg\DocuWare\Responses\Authentication\OAuth;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Authentication\OAuth\ResponsibleIdentityService;
use CodebarAg\DocuWare\Support\ResponseBody;
use Saloon\Http\Response;

final class GetResponsibleIdentityServiceResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): ResponsibleIdentityService
    {
        $response = self::validated($response);

        return ResponsibleIdentityService::make(ResponseBody::toArray($response));
    }
}
