<?php

namespace CodebarAg\DocuWare\Responses\General\Organization;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use Saloon\Http\Response;

final class RequestLoginTokenResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): string
    {
        $response = self::validated($response);

        return $response->body();
    }
}
