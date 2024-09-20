<?php

namespace CodebarAg\DocuWare\Responses\Documents\Sections;

use CodebarAg\DocuWare\DTO\Textshot;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class GetTextshotResponse
{
    public static function fromResponse(Response $response): Textshot
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return Textshot::fromJson($response->throw()->json());
    }
}
