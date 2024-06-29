<?php

namespace CodebarAg\DocuWare\Responses\Documents\Download;

use CodebarAg\DocuWare\DTO\Documents\DocumentThumbnail;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class DownloadThumbnailResponse
{
    public static function fromResponse(Response $response): DocumentThumbnail
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return DocumentThumbnail::fromData([
            'mime' => $response->throw()->header('Content-Type'),
            'data' => $response->throw()->body(),
        ]);
    }
}
