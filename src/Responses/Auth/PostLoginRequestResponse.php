<?php

namespace CodebarAg\DocuWare\Responses\Fields;

use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Contracts\Response;

final class PostLoginRequestResponse
{
    public static function fromResponse(Response $response)
    {
        EnsureValidResponse::from($response);

        return $response->throw()->json();
    }
}
