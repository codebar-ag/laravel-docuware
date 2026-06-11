<?php

namespace CodebarAg\DocuWare\Responses\Documents\DocumentsTrashBin;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Documents\DocumentsTrashBin\RestoreDocuments;
use Saloon\Http\Response;

final class RestoreDocumentsResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): RestoreDocuments
    {
        $response = self::validated($response);

        return RestoreDocuments::fromData($response->throw()->json());
    }
}
