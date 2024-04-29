<?php

namespace CodebarAg\DocuWare\DTO\Documents\DocumentIndex;

class IndexNumericDTO
{
    public string $type;

    public function __construct(
        public string $name,
        public ?int $value,
    ) {

    }

    public static function make(string $name, ?int $value): self
    {
        return new self($name, $value);
    }

    public function values(): array
    {
        return [
            'FieldName' => $this->name,
            'Item' => $this->value,
            'ItemElementName' => 'Int',
        ];
    }
}
