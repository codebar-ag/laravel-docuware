<?php

namespace CodebarAg\DocuWare\Responses\Documents\ModifyDocuments;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use Saloon\Http\Response;

final class TransferDocumentResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): bool
    {
        $response = self::validated($response);

        return $response->successful();
    }
}
