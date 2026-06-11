<?php

namespace CodebarAg\DocuWare\Responses\Documents\Download;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Documents\DocumentThumbnail;
use Saloon\Http\Response;

final class DownloadThumbnailResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): DocumentThumbnail
    {
        $response = self::validated($response);

        return DocumentThumbnail::fromData([
            'mime' => $response->throw()->header('Content-Type'),
            'data' => $response->throw()->body(),
        ]);
    }
}
