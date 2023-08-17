<?php

namespace CodebarAg\DocuWare\Requests\Document;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetDocumentsDownloadRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
        protected readonly array $additionalDocumentIds,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/FileDownload';
    }

    public function defaultQuery(): array
    {
        return [
            'keepAnnotations' => 'false',
            'append' => implode(',', $this->additionalDocumentIds),
        ];
    }
}
