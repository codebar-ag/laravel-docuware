<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Collection;

class DocumentTableIndex
{
    public string $type;

    public function __construct(
        public string $name,
        public array $value,
    ) {
    }

    public static function make(string $name, array $value): self
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
            '$type' => 'DocumentIndexFieldTable',
            'Row' => [
                'ColumnValue' => $indexes
                    ->map(fn (DocumentTableIndex $index) => $index->values())
                    ->toArray(),
            ],
        ];

        return json_encode($indexContent);
    }
}
