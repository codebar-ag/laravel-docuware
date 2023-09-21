<?php

namespace CodebarAg\DocuWare\Requests\Document;

use CodebarAg\DocuWare\Responses\Document\PostTransferDocumentResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class PostTransferDocumentRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $documentId,
        protected readonly string $fileCabinetId,
        protected readonly string $destinationFileCabinetId,
        protected readonly string $storeDialogId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->destinationFileCabinetId.'/Task/Transfer';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/vnd.docuware.platform.documentstransferinfo+json',
            'X-Requested-With' => 'XMLHttpRequest',
        ];
    }

    protected function defaultBody(): array
    {
        return [
            'SourceFileCabinetId' => $this->fileCabinetId,
            'Documents' => [
                [
                    'Id' => $this->documentId,
                    'Field' => null,
                ],
            ],
            'KeepSource' => false,
            'FillIntellix' => false,
            'UseDefaultDialog' => false,
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return PostTransferDocumentResponse::fromResponse($response);
    }
}
