<?php

namespace CodebarAg\DocuWare\Requests\Document;

use CodebarAg\DocuWare\Exceptions\UnableToUpdateFields;
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
        protected readonly array $values,
        protected readonly bool $forceUpdate = false,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/Fields';
    }

    public function defaultBody(): array
    {
        throw_unless(count($this->values) > 0, UnableToUpdateFields::noValuesProvided());

        $fields = [];

        foreach ($this->values as $key => $value) {
            throw_unless($value, UnableToUpdateFields::noValuesProvidedForField($key));
            $fields[] = [
                'FieldName' => $key,
                'Item' => $value,
            ];
        }

        $content = [
            'Field' => $fields,
        ];

        if ($this->forceUpdate) {
            $content['ForceUpdate'] = true;
        }

        return $content;
    }
}
