<?php

namespace CodebarAg\DocuWare\Responses\Authentication\OAuth;

use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken as RequestTokenDto;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class RequestTokenResponse
{
    public static function fromResponse(Response $response): RequestTokenDto
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return RequestTokenDto::make($response->json());
    }
}
