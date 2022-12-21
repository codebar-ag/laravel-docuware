<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Collection;

class DocumentIndex
{
    public string $type;

    public function __construct(
        public string $name,
        public int|string $value,
    ) {
        $this->type = is_int($value) ? 'Int' : 'String';
    }

    public static function make(string $name, int|string $value): self
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
}
