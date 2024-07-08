<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\Upload;

use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\Responses\FileCabinets\Upload\CreateDataRecordResponse;
use Illuminate\Support\Collection;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasMultipartBody;

class AppendFilesToADataRecord extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $dataRecordId,
        protected readonly Collection $files,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->dataRecordId;
    }

    protected function defaultBody(): array
    {
        return $this->files->toArray();
    }

    public function createDtoFromResponse(Response $response): Document
    {
        return CreateDataRecordResponse::fromResponse($response);
    }
}
