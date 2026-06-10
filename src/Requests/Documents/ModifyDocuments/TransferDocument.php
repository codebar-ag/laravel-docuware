<?php

namespace CodebarAg\DocuWare\Requests\Documents\ModifyDocuments;

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateTimeDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDecimalDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexKeywordDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexMemoDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexNumericDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTableDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\PrepareDTO;
use CodebarAg\DocuWare\Responses\Documents\ModifyDocuments\TransferDocumentResponse;
use Illuminate\Support\Arr;
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

    /**
     * @param  Collection<int, IndexTextDTO|IndexDateDTO|IndexDateTimeDTO|IndexNumericDTO|IndexDecimalDTO|IndexTableDTO|IndexKeywordDTO|IndexMemoDTO>|null  $fields
     */
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
        $endpoint = '/FileCabinets/'.$this->destinationFileCabinetId.'/Task/Transfer';

        if ($this->storeDialogId) {
            $endpoint .= '?StoreDialogId='.$this->storeDialogId;
        }

        return $endpoint;
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/vnd.docuware.platform.documentstransferinfo+json',
            'X-Requested-With' => 'XMLHttpRequest',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        $body = [
            'SourceFileCabinetId' => $this->fileCabinetId,
            'Documents' => [
                [
                    'Id' => $this->documentId,
                    'Fields' => $this->fields ? Arr::get(PrepareDTO::makeField($this->fields), 'Field') : null,
                ],
            ],
            'KeepSource' => $this->keepSource,
            'FillIntellix' => $this->fillIntellix,
            'UseDefaultDialog' => $this->useDefaultDialog,
        ];

        return $body;
    }

    public function createDtoFromResponse(Response $response): bool
    {
        return TransferDocumentResponse::fromResponse($response);
    }
}
