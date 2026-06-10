<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\Upload;

use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateTimeDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDecimalDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexKeywordDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexMemoDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexNumericDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTableDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\PrepareDTO;
use CodebarAg\DocuWare\Responses\FileCabinets\Upload\CreateDataRecordResponse;
use Illuminate\Support\Collection;
use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasMultipartBody;

class CreateDataRecord extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;

    /**
     * @param  Collection<int, IndexTextDTO|IndexDateDTO|IndexDateTimeDTO|IndexNumericDTO|IndexDecimalDTO|IndexTableDTO|IndexKeywordDTO|IndexMemoDTO>|null  $indexes
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly ?string $fileContent,
        protected readonly ?string $fileName,
        protected readonly ?Collection $indexes = null,
        protected readonly ?string $storeDialogId = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents';
    }

    /**
     * @return array<string, string>
     */
    public function defaultQuery(): array
    {
        if ($this->storeDialogId === null || $this->storeDialogId === '') {
            return [];
        }

        return [
            'StoreDialogId' => $this->storeDialogId,
        ];
    }

    /**
     * @return list<MultipartValue>
     */
    protected function defaultBody(): array
    {
        $body = [];

        if ($this->indexes) {
            $indexContent = json_encode(PrepareDTO::makeFields($this->indexes));
            $body[] = new MultipartValue(name: 'document', value: $indexContent, filename: 'index.json');
        }

        if ($this->fileContent && $this->fileName) {
            $body[] = new MultipartValue(name: 'file', value: $this->fileContent, filename: $this->fileName);
        }

        return $body;
    }

    public function createDtoFromResponse(Response $response): Document
    {
        return CreateDataRecordResponse::fromResponse($response);
    }
}
