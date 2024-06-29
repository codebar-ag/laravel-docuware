<?php

namespace CodebarAg\DocuWare\Requests\Documents\ModifyDocuments;

use CodebarAg\DocuWare\Responses\Documents\ModifyDocuments\TransferDocumentResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class TransferDocument extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $destinationFileCabinetId,
        protected readonly string $storeDialogId,
        protected readonly string $documentId,
        protected readonly array $fields = [],
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
                    'Fields' => $this->fields,
                ],
            ],
            'KeepSource' => false,
            'FillIntellix' => false,
            'UseDefaultDialog' => false,
        ];
    }

    public function createDtoFromResponse(Response $response): bool
    {
        return TransferDocumentResponse::fromResponse($response);
    }
}
