<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DocumentIndex
{
    public string $type;

    public function __construct(
        public string $name,
        public int|string|float|array $value,
    ) {
        $this->type = self::type(gettype($value));
        $this->value = self::value($this->type, $this->value);
    }

    public static function make(string $name, int|string|float|array $value): self
    {
        return new self($name, $value);
    }

    public function values(): array
    {
        return [
            'FieldName' => $this->name,
            'Item' => $this->value,
            'ItemElementName' => $this->type,
        ];
    }

    public static function makeContent(Collection $indexes): string
    {
        $indexContent = (object) [
            'Fields' => $indexes
                ->map(fn (DocumentIndex $index) => $index->values())
                ->toArray(),
        ];

        return json_encode($indexContent);
    }

    public static function makeColumnValue(Collection $indexes): object
    {
        $columnValues = (object) [
            'ColumnValue' => $indexes
                ->map(fn (DocumentIndex $index) => $index->values())
                ->toArray(),
        ];

        return $columnValues;
    }

    protected function value(string $type, int|string|float|array $value): int|string|float|array|null
    {
        return match ($type) {
            'Int' => (int) $value,
            'String' => html_entity_decode($value),
            'Decimal' => (float) $value,
            'Table' => self::table($value),
            default => null,
        };
    }

    protected function type(string $type): ?string
    {
        return match ($type) {
            'integer' => 'Int',
            'string' => 'String',
            'double' => 'Decimal',
            'array' => 'Table',
            default => null,
        };
    }

    protected function table(array $value): array
    {
        $values = Arr::get($value, 'VALUES', []);
        $fields = Arr::get($value, 'FIELDS', []);

        $rows = collect();

        collect($values)->each(function ($item) use ($fields, $rows) {

            if (Arr::get($item, 'LineItemType') != 'NORMAL') {
                return;
            }

            $rows->push(self::row($fields, $item));
        })->toArray();

        return [
            '$type' => 'DocumentIndexFieldTable',
            'Row' => $rows,
        ];

    }

    protected function row(array $fields, array $values)
    {
        $indexes = collect();

        collect($fields)->each(function ($item) use ($indexes, $values) {

            $field = Arr::get($item, 'FIELD');
            $reference = Arr::get($item, 'REFERENCE');
            $type = Arr::get($item, 'TYPE');

            $value = Arr::has($item, 'VALUE')
                ? Arr::get($item, 'VALUE')
                : Arr::get($values, $reference);

            if (! $value || ! $field) {
                return;
            }

            if (in_array($type, ['String', 'Integer', 'Decimal', 'Date'])) {
                $indexes->push(DocumentIndex::make($field, $value));
            }
        });

        return DocumentIndex::makeColumnValue($indexes);
    }
}
