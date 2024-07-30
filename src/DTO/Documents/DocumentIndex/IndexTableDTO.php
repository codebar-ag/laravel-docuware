<?php

namespace CodebarAg\DocuWare\DTO\Documents\DocumentIndex;

use Illuminate\Support\Collection;

class IndexTableDTO
{
    public function __construct(
        public string $name,
        public null|Collection|array $rows,
    ) {}

    public static function make(string $name, Collection|array $rows): self
    {
        return new self($name, $rows);
    }

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

    protected function rowsCollection(): array
    {
        return collect($this->rows ?? [])->map(function ($row) {
            return self::makeRowContent(collect($row));
        })
            ->filter()
            ->values()
            ->toArray();
    }

    public static function makeRowContent(Collection $indexes): array
    {
        return [
            'ColumnValue' => $indexes
                ->map(fn (IndexTextDTO|IndexNumericDTO|IndexDecimalDTO|IndexDateDTO|IndexDateTimeDTO $index) => $index->values())
                ->filter()
                ->values()
                ->toArray(),
        ];
    }
}
