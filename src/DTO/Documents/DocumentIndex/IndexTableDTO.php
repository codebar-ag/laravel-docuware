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
            ->map(function (mixed $row): array {
                $rowCollection = $row instanceof Collection
                    ? $row
                    : collect(is_array($row) ? $row : []);

                return self::makeRowContent($rowCollection);
            })
            ->filter()
            ->values()
            ->toArray();
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
