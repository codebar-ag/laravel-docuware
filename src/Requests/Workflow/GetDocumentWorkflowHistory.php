<?php

namespace CodebarAg\DocuWare\Requests\Workflow;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\Workflow\InstanceHistory;
use CodebarAg\DocuWare\Responses\Workflow\GetDocumentWorkflowHistoryResponse;
use Illuminate\Support\Collection;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetDocumentWorkflowHistory extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/WorkflowHistory';
    }

    /**
     * @return Collection<int, InstanceHistory>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return GetDocumentWorkflowHistoryResponse::fromResponse($response);
    }
}
