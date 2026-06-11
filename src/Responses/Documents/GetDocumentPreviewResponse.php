<?php

namespace CodebarAg\DocuWare\Responses\Documents;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use Saloon\Http\Response;

final class GetDocumentPreviewResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): string
    {
        $response = self::validated($response);

        return $response->throw()->body();
    }
}
