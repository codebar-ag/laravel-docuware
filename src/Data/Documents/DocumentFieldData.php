<?php

namespace CodebarAg\DocuWare\Data\Documents;

use Carbon\Carbon;
use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\Support\ParseFieldValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * A single index field on a document, with its value parsed to a native type.
 */
final class DocumentFieldData extends DocuWareData
{
    /**
     * @param  null|int|float|Carbon|string|Collection<int, TableRowData>  $value
     */
    public function __construct(
        public bool $systemField,
        public string $name,
        public string $label,
        public bool $isNull,
        public null|int|float|Carbon|string|Collection $value,
        public string $type,
        public ?bool $readOnly = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            systemField: (bool) Arr::get($data, 'SystemField'),
            name: (string) Arr::get($data, 'FieldName'),
            label: (string) Arr::get($data, 'FieldLabel'),
            isNull: (bool) Arr::get($data, 'IsNull'),
            value: ParseFieldValue::field($data),
            type: (string) Arr::get($data, 'ItemElementName'),
            readOnly: Arr::get($data, 'ReadOnly'),
        );
    }
}
