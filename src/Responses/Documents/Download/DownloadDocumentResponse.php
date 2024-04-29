<?php

namespace CodebarAg\DocuWare\Responses\Documents\Download;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class DownloadDocumentResponse
{
    public static function fromResponse(Response $response): string
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->body();
    }
}
