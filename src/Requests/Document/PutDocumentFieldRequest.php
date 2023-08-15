<?php

namespace CodebarAg\DocuWare\Requests\Document;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class PutDocumentFieldRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
        protected readonly string $fieldName,
        protected readonly string $newValue,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/Fields';
    }

    public function defaultBody(): array
    {
        return [
            'Field' => [
                [
                    'FieldName' => $this->fieldName,
                    'Item' => $this->newValue,
                ],
            ],
        ];
    }
}
