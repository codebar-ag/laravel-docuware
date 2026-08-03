<?php

namespace CodebarAg\DocuWare\DTO\Documents\DocumentIndex;

use Illuminate\Support\Collection;

class IndexTableDTO
{
    /**
     * @param  array<int, mixed>|Collection<int, mixed>|null  $rows
     */
    public function __construct(
        public string $name,
        public null|Collection|array $rows,
    ) {}

    /**
     * @param  array<int, mixed>|Collection<int, mixed>  $rows
     */
    public static function make(string $name, Collection|array $rows): self
    {
        return new self($name, $rows);
    }

    /**
     * @return array<string, mixed>
     */
    public function values(): array
    {
        return [
            'FieldName' => $this->name,
            'Item' => [
                '$type' => 'DocumentIndexFieldTable',
                'Row' => self::rowsCollection(),
            ],
            'ItemElementName' => 'Table',
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function rowsCollection(): array
    {
        $rows = $this->rows ?? [];

        $collection = $rows instanceof Collection
            ? $rows
            : collect($rows);

        return $collection
            ->map(fn (mixed $row): array => self::makeRowContent(self::normalizeRow($row)))
            ->filter(fn (array $content): bool => $content['ColumnValue'] !== [])
            ->values()
            ->toArray();
    }

    /**
     * Normalize a single row into a collection of typed index DTOs. Accepts either:
     *  - a list/collection of {@see IndexTextDTO} (and siblings) for explicit typing, or
     *  - an associative `[columnName => scalar|Carbon]` map whose cell types are auto-detected
     *    ({@see IndexDetectDTO}: string→String, int→Int, float→Decimal, Carbon→DateTime).
     *
     * @return Collection<int, IndexTextDTO|IndexNumericDTO|IndexDecimalDTO|IndexDateDTO|IndexDateTimeDTO|IndexKeywordDTO|IndexMemoDTO>
     */
    protected static function normalizeRow(mixed $row): Collection
    {
        $entries = $row instanceof Collection
            ? $row
            : collect(is_array($row) ? $row : []);

        return $entries
            ->map(function (mixed $value, int|string $key) {
                if ($value instanceof IndexTextDTO
                    || $value instanceof IndexNumericDTO
                    || $value instanceof IndexDecimalDTO
                    || $value instanceof IndexDateDTO
                    || $value instanceof IndexDateTimeDTO
                    || $value instanceof IndexKeywordDTO
                    || $value instanceof IndexMemoDTO) {
                    return $value;
                }

                return is_string($key) ? IndexDetectDTO::make($key, $value) : null;
            })
            ->filter()
            ->values();
    }

    /**
     * @param  Collection<int, IndexTextDTO|IndexNumericDTO|IndexDecimalDTO|IndexDateDTO|IndexDateTimeDTO|IndexKeywordDTO|IndexMemoDTO>  $indexes
     * @return array<string, list<array<string, mixed>>>
     */
    public static function makeRowContent(Collection $indexes): array
    {
        return [
            'ColumnValue' => $indexes
                ->map(fn (IndexTextDTO|IndexNumericDTO|IndexDecimalDTO|IndexDateDTO|IndexDateTimeDTO|IndexKeywordDTO|IndexMemoDTO $index) => $index->values())
                ->filter()
                ->values()
                ->toArray(),
        ];
    }
}
