<?php

namespace CodebarAg\DocuWare\Responses\Documents\DocumentsTrashBin;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Documents\DocumentsTrashBin\DeleteDocuments;
use Saloon\Http\Response;

final class DeleteDocumentsResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): DeleteDocuments
    {
        $response = self::validated($response);

        return DeleteDocuments::fromData($response->throw()->json());
    }
}
