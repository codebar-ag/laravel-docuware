<?php

namespace CodebarAg\DocuWare\Responses\Document;

use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class GetDocumentResponse
{
    public static function fromResponse(Response $response): Document
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $data = $response->throw()->json();

        return Document::fromJson($data);
    }
}
