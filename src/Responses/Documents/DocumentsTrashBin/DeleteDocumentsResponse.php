<?php

namespace CodebarAg\DocuWare\Responses\Documents\DocumentsTrashBin;

use CodebarAg\DocuWare\DTO\Documents\DocumentsTrashBin\DeleteDocuments;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class DeleteDocumentsResponse
{
    public static function fromResponse(Response $response): DeleteDocuments
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return DeleteDocuments::fromData($response->throw()->json());
    }
}
