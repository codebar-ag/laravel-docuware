<?php

namespace CodebarAg\DocuWare\Responses\History;

use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Exception;
use Saloon\Http\Response;

final class GetWorkflowDocumentHistoryResponse
{
    public static function fromResponse(Response $response): mixed
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->json();
    }
}
