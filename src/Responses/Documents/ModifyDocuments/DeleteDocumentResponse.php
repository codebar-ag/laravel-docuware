<?php

namespace CodebarAg\DocuWare\Responses\Documents\ModifyDocuments;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use Saloon\Http\Response;

final class DeleteDocumentResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): Response
    {
        $response = self::validated($response);

        return $response->throw();
    }
}
