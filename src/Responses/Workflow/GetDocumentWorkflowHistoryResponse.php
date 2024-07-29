<?php

namespace CodebarAg\DocuWare\Responses\Workflow;

use CodebarAg\DocuWare\DTO\Workflow\InstanceHistory;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetDocumentWorkflowHistoryResponse
{
    public static function fromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $instanceHistories = $response->throw()->json('InstanceHistory');

        return collect($instanceHistories)->map(fn (array $instanceHistory) => InstanceHistory::fromJson($instanceHistory));
    }
}
