<?php

namespace CodebarAg\DocuWare\Requests\Documents\ModifyDocuments;

use CodebarAg\DocuWare\Responses\Documents\ModifyDocuments\DeleteDocumentResponse;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class DeleteDocument extends Request
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

    public function createDtoFromResponse(Response $response): Response
    {
        return DeleteDocumentResponse::fromResponse($response);
    }
}
