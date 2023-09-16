<?php

namespace CodebarAg\DocuWare\Responses\Document\Thumbnail;

use CodebarAg\DocuWare\DTO\DocumentThumbnail;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Contracts\Response;

final class GetDocumentDownloadThumbnailResponse
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
