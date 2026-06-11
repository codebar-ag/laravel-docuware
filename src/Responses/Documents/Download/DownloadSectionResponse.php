<?php

namespace CodebarAg\DocuWare\Responses\Documents\Download;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use Saloon\Http\Response;

final class DownloadSectionResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): mixed
    {
        $response = self::validated($response);

        return $response->throw()->body();
    }
}
