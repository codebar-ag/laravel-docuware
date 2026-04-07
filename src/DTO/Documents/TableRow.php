<?php

namespace CodebarAg\DocuWare\DTO\Documents;

use Illuminate\Support\Collection;

class TableRow
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        $fieldList = [];
        foreach (array_values($data) as $item) {
            if (is_array($item)) {
                $fieldList[] = $item;
            }
        }

        $fields = self::convertFields(collect($fieldList));

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
        return $fields->mapWithKeys(function (array $field) {
            return [$field['FieldName'] => DocumentField::fromJson($field)];
        });
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
