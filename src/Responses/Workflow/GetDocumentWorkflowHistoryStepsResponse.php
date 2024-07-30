<?php

namespace CodebarAg\DocuWare\Responses\Workflow;

use CodebarAg\DocuWare\DTO\Workflow\InstanceHistory;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class GetDocumentWorkflowHistoryStepsResponse
{
    public static function fromResponse(Response $response): InstanceHistory
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return InstanceHistory::fromJson($response->throw()->json());
    }
}
