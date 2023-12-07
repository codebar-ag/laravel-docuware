<?php

namespace CodebarAg\DocuWare\DTO\DocumentIndex;

class IndexDecimalDTO
{
    public function __construct(
        public string $name,
        public null|int|float $value,
    ) {

    }

    public static function make(string $name, null|int|float $value): self
    {
        return new self($name, $value);
    }

    public function values(): array
    {
        return [
            'FieldName' => $this->name,
            'Item' => (float) $this->value,
            'ItemElementName' => 'Decimal',
        ];
    }
}
