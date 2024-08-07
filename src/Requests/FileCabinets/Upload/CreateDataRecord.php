<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\Upload;

use CodebarAg\DocuWare\DTO\Documents\Document;
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

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly ?string $fileContent,
        protected readonly ?string $fileName,
        protected readonly ?Collection $indexes = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents';
    }

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
