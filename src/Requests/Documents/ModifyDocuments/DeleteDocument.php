<?php

namespace CodebarAg\DocuWare\Requests\Documents\ModifyDocuments;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteDocument extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId;
    }
}
