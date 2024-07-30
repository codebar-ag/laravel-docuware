<?php

namespace CodebarAg\DocuWare\Responses\General\Organization;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class RequestLoginTokenResponse
{
    public static function fromResponse(Response $response): string
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->body();
    }
}
