<?php

namespace CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin;

use CodebarAg\DocuWare\DTO\Documents\DocumentsTrashBin\RestoreDocuments as RestoreDocumentsDto;
use CodebarAg\DocuWare\Responses\Documents\DocumentsTrashBin\RestoreDocumentsResponse;
use Illuminate\Support\Collection;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class RestoreDocuments extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly array|Collection $ids = [],
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/TrashBin/BatchRestore';
    }

    public function defaultBody(): array
    {
        return [
            'Id' => $this->ids instanceof Collection ? $this->ids->toArray() : $this->ids,
        ];
    }

    public function createDtoFromResponse(Response $response): RestoreDocumentsDto
    {
        return RestoreDocumentsResponse::fromResponse($response);
    }
}
