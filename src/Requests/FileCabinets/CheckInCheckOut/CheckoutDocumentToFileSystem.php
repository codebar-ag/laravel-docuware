<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\CheckInCheckOut;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class CheckoutDocumentToFileSystem extends Request
{
    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly int|string $documentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/CheckoutToFileSystem';
    }
}
