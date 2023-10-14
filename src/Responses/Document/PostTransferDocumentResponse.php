<?php

namespace CodebarAg\DocuWare\Responses\Document;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class PostTransferDocumentResponse
{
    public static function fromResponse(Response $response): bool
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->successful();
    }
}
