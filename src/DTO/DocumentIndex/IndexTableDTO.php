<?php

namespace CodebarAg\DocuWare\DTO\DocumentIndex;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class IndexTableDTO
{
    public function __construct(
        public string $name,
        public null|Collection|array $rows,
    ) {}

    public static function make(string $name, null|Collection|array $rows): self
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

            $indexes = collect($row)->map(function ($column) {

                if (! Arr::has($column, ['NAME', 'VALUE'])) {
                    return null;
                }

                $name = Arr::get($column, 'NAME');
                $value = Arr::get($column, 'VALUE');

                return PrepareTableDTO::make($name, $value);

            })
                ->filter()
                ->values();

            if ($indexes->isEmpty()) {
                return null;
            }

            return self::makeRowContent($indexes);

        })
            ->filter()
            ->values()
            ->toArray();

    }

    public static function makeRowContent(Collection $indexes): array
    {
        return [
            'ColumnValue' => $indexes
                ->map(fn (IndexTextDTO|IndexDateDTO|IndexDecimalDTO $index) => $index->values())
                ->filter()
                ->values()
                ->toArray(),
        ];
    }
}
