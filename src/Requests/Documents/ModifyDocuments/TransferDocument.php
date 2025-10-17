<?php

namespace CodebarAg\DocuWare\Requests\Documents\ModifyDocuments;

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\PrepareDTO;
use CodebarAg\DocuWare\Responses\Documents\ModifyDocuments\TransferDocumentResponse;
use Illuminate\Support\Collection;
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
        protected readonly string $documentId,
        protected readonly ?string $storeDialogId = null,
        protected readonly ?Collection $fields = null,
        protected readonly bool $keepSource = false,
        protected readonly bool $fillIntellix = false,
        protected readonly bool $useDefaultDialog = false,
    ) {}

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
        $body = [
            'SourceFileCabinetId' => $this->fileCabinetId,
            'Documents' => [
                [
                    'Id' => $this->documentId,
                ],
            ],
            'KeepSource' => $this->keepSource,
            'FillIntellix' => $this->fillIntellix,
            'UseDefaultDialog' => $this->useDefaultDialog,
        ];

        if ($this->storeDialogId) {
            $body['StoreDialogId'] = $this->storeDialogId;
        }

        if ($this->fields) {
            $fieldData = PrepareDTO::makeField($this->fields);
            $body['Documents'][0]['Fields'] = $fieldData['Field'];
        }

        return $body;
    }

    public function createDtoFromResponse(Response $response): bool
    {
        return TransferDocumentResponse::fromResponse($response);
    }
}
