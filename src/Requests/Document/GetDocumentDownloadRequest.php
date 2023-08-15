<?php

namespace CodebarAg\DocuWare\Requests\Document;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetDocumentDownloadRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
        protected readonly ?array $additionalDocumentIds = null,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/FileDownload';
    }

    public function defaultQuery(): array
    {
        $defaultQuery = [
            'targetFileType' => 'Auto',
            'keepAnnotations' => 'false',
        ];

        if ($this->additionalDocumentIds) {
            $defaultQuery['additionalDocumentIds'] = implode(',', $this->additionalDocumentIds);
        }

        return $defaultQuery;
    }
}
