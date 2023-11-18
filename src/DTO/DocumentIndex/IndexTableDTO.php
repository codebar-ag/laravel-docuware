<?php

namespace CodebarAg\DocuWare\DTO\DocumentIndex;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class IndexTableDTO
{
    public function __construct(
        public string $name,
        public array $rows,
    ) {

    }

    public static function make(string $name, array $rows): self
    {
        return new self($name, $rows);
    }

    public function values(): array
    {
        return [
            'FieldName' => $this->name,
            'Item' => [
                '$type' => 'DocumentIndexFieldTable',
                'Row' => self::rows(),
            ],
            'ItemElementName' => 'Table',
        ];
    }

    protected function rows(): array
    {
        return collect($this->rows)->each(function ($row) {
            $indexes = collect($row)->map(function ($column) {
                $name = Arr::get($column, 'NAME');
                $value = Arr::get($column, 'VALUE');

                return PrepareTableDTO::make($name, $value);
            });

            return self::makeRowContent($indexes);
        })->filter()->values()->toArray();
    }

    public static function makeRowContent(Collection $indexes): object
    {
        $row = (object) [
            'ColumnValue' => $indexes
                ->map(fn (IndexTextDTO|IndexDateDTO|IndexDecimalDTO $index) => $index->values())
                ->filter()
                ->values()
                ->toArray(),
        ];

        return $row;
    }
}
