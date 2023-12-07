<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Collection;

class TableRow
{
    public static function fromJson(array $data): self
    {
        $fields = self::convertFields(collect($data));

        return new self(
            fields: $fields,
        );
    }

    protected static function convertFields(Collection $fields): Collection
    {
        return $fields->mapWithKeys(function (array $field) {
            return [$field['FieldName'] => DocumentField::fromJson($field)];
        });
    }

    public function __construct(
        public Collection $fields,
    ) {
    }

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
