<?php

namespace CodebarAg\DocuWare\Responses\Documents\ApplicationProperties;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class GetApplicationPropertiesResponse
{
    public static function fromResponse(Response $response): mixed
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->json();
    }
}
