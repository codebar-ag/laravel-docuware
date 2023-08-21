<?php

namespace CodebarAg\DocuWare\Requests\Document\Thumbnail;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetDocumentDownloadThumbnailRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
        protected readonly string $section,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Rendering/'.$this->documentId.'-'.$this->section.'/Thumbnail';
    }

    public function defaultQuery(): array
    {
        return [
            'page' => 0,
        ];
    }
}
