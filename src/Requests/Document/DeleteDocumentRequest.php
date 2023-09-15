<?php

namespace CodebarAg\DocuWare\Requests\Document;

use CodebarAg\DocuWare\Responses\Document\DeleteDocumentResponse;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteDocumentRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId;
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return DeleteDocumentResponse::fromResponse($response);
    }
}
