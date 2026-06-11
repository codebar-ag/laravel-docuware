<?php

namespace CodebarAg\DocuWare\Responses\Workflow;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Workflow\InstanceHistory;
use Saloon\Http\Response;

final class GetDocumentWorkflowHistoryStepsResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): InstanceHistory
    {
        $response = self::validated($response);

        return InstanceHistory::fromJson($response->throw()->json());
    }
}
