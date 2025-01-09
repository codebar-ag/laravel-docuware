<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\Upload;

use CodebarAg\DocuWare\DTO\Section;
use CodebarAg\DocuWare\Responses\FileCabinets\Upload\AppendSinglePDFToADocumentResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasMultipartBody;

class AppendASinglePDFToADocument extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
        protected readonly ?string $fileContent,
        protected readonly ?string $fileName,

    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Sections';
    }

    protected function defaultQuery(): array
    {
        return [
            'DocId' => $this->documentId,
        ];
    }

    protected function defaultBody(): array
    {
        return [
            new MultipartValue(name: 'file', value: $this->fileContent, filename: $this->fileName),
        ];
    }

    public function createDtoFromResponse(Response $response): Section
    {
        return AppendSinglePDFToADocumentResponse::fromResponse($response);
    }
}
