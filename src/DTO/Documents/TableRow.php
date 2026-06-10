<?php

namespace CodebarAg\DocuWare\DTO\Documents;

use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class TableRow
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        $fields = self::convertFields(collect(JsonArrays::listOfRecords($data)));

        return new self(
            fields: $fields,
        );
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $fields
     * @return Collection<string, DocumentField>
     */
    protected static function convertFields(Collection $fields): Collection
    {
        return $fields
            ->filter(fn (array $field) => is_string(Arr::get($field, 'FieldName')) && Arr::get($field, 'FieldName') !== '')
            ->mapWithKeys(fn (array $field) => [Arr::get($field, 'FieldName') => DocumentField::fromJson($field)]);
    }

    /**
     * @param  Collection<string, DocumentField>  $fields
     */
    public function __construct(
        public Collection $fields,
    ) {}

    /**
     * @param  Collection<string, DocumentField>|null  $fields
     */
    public static function fake(
        ?Collection $fields = null,
    ): self {
        return new self(
            fields: $fields ?? collect([
                DocumentField::fake(),
                DocumentField::fake(),
            ]),
        );
    }
}
