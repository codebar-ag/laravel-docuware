<?php

namespace CodebarAg\DocuWare\Responses\Authentication\OAuth;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken as RequestTokenDto;
use CodebarAg\DocuWare\Support\ResponseBody;
use Saloon\Http\Response;

final class RequestTokenResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): RequestTokenDto
    {
        $response = self::validated($response);

        return RequestTokenDto::make(ResponseBody::toArray($response));
    }
}
