<?php

namespace CodebarAg\DocuWare\Requests\Document;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetDocumentPreviewRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/Image';
    }
}
