<?php

namespace CodebarAg\DocuWare\Requests\Document;

use CodebarAg\DocuWare\DTO\DocumentIndex;
use CodebarAg\DocuWare\Responses\Document\PostDocumentResponse;
use Illuminate\Support\Collection;
use Saloon\Contracts\Body\HasBody;
use Saloon\Contracts\Response;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasMultipartBody;

class PostDocumentRequest extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $fileContent,
        protected readonly string $fileName,
        protected readonly ?Collection $indexes = null,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents';
    }

    protected function defaultBody(): array
    {
        $body = [];

        if ($this->indexes) {
            $indexContent = DocumentIndex::makeContent($this->indexes);

            $body[] = new MultipartValue(name: 'document', value: $indexContent, filename: 'index.json');
        }

        $body[] = new MultipartValue(name: 'file', value: $this->fileContent, filename: $this->fileName);

        return $body;
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return PostDocumentResponse::fromResponse($response);
    }
}
