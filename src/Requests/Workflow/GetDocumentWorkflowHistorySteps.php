<?php

namespace CodebarAg\DocuWare\Requests\Workflow;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\Workflow\InstanceHistory;
use CodebarAg\DocuWare\Responses\Workflow\GetDocumentWorkflowHistoryStepsResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetDocumentWorkflowHistorySteps extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $workflowId,
        protected readonly string $workflowInstanceId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/Workflows/'.$this->workflowId.'/Instances/'.$this->workflowInstanceId.'/History';
    }

    public function createDtoFromResponse(Response $response): InstanceHistory
    {
        return GetDocumentWorkflowHistoryStepsResponse::fromResponse($response);
    }
}
