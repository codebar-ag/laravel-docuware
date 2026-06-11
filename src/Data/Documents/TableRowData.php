<?php

namespace CodebarAg\DocuWare\Data\Documents;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * A row of a table index field — its cells keyed by field DB name.
 */
final class TableRowData extends DocuWareData
{
    /**
     * @param  Collection<string, DocumentFieldData>  $fields
     */
    public function __construct(
        public Collection $fields,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        $fields = collect(JsonArrays::listOfRecords($data))
            ->filter(fn (array $field) => is_string(Arr::get($field, 'FieldName')) && Arr::get($field, 'FieldName') !== '')
            ->mapWithKeys(fn (array $field) => [Arr::get($field, 'FieldName') => DocumentFieldData::fromDocuWare($field)]);

        return new self(fields: $fields);
    }
}
