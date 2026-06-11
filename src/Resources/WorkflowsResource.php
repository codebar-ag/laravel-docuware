<?php

namespace CodebarAg\DocuWare\Resources;

use CodebarAg\DocuWare\Data\Workflow\InstanceHistoryData;
use CodebarAg\DocuWare\Requests\Workflow\GetDocumentWorkflowHistorySteps;

/**
 * Workflow instance history. Reached via `DocuWare::workflows()`.
 */
final class WorkflowsResource extends Resource
{
    public function historySteps(string $workflowId, string $workflowInstanceId): InstanceHistoryData
    {
        $response = $this->send(new GetDocumentWorkflowHistorySteps($workflowId, $workflowInstanceId));

        return InstanceHistoryData::fromDocuWare($response->json());
    }
}
