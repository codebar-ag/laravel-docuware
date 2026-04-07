<?php

namespace CodebarAg\DocuWare\Responses\Workflow;

use CodebarAg\DocuWare\DTO\Workflow\InstanceHistory;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class GetDocumentWorkflowHistoryResponse
{
    /**
     * @return Collection<int, InstanceHistory>
     */
    public static function fromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        if ($response->status() === SymfonyResponse::HTTP_NOT_FOUND) {
            return collect();
        }

        EnsureValidResponse::from($response);

        $instanceHistories = $response->throw()->json('InstanceHistory');

        return collect(JsonArrays::listOfRecords($instanceHistories))->map(fn (array $instanceHistory) => InstanceHistory::fromJson($instanceHistory));
    }
}
