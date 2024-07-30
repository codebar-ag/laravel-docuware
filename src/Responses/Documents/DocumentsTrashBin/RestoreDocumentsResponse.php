<?php

namespace CodebarAg\DocuWare\Responses\Documents\DocumentsTrashBin;

use CodebarAg\DocuWare\DTO\Documents\DocumentsTrashBin\RestoreDocuments;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class RestoreDocumentsResponse
{
    public static function fromResponse(Response $response): RestoreDocuments
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return RestoreDocuments::fromData($response->throw()->json());
    }
}
