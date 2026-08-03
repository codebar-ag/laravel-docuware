<?php

namespace CodebarAg\DocuWare\DTO\Documents\DocumentIndex;

class IndexDecimalDTO
{
    public function __construct(
        public string $name,
        public null|int|float $value,
    ) {}

    public static function make(string $name, null|int|float $value): self
    {
        return new self($name, $value);
    }

    /**
     * @return array<string, mixed>
     */
    public function values(): array
    {
        return [
            'FieldName' => $this->name,
            'Item' => $this->value === null ? null : (float) $this->value,
            'ItemElementName' => 'Decimal',
        ];
    }
}
