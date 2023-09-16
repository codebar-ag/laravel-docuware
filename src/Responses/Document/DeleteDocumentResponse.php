<?php

namespace CodebarAg\DocuWare\Responses\Document;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Contracts\Response;

final class DeleteDocumentResponse
{
    public static function fromResponse(Response $response): Response
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw();
    }
}
