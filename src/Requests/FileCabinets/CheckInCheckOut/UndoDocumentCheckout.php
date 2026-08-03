<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\CheckInCheckOut;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class UndoDocumentCheckout extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly int|string $documentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Operations/ProcessDocumentAction?DocId='.$this->documentId;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'DocumentAction' => 'UndoCheckOut',
        ];
    }
}
