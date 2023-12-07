<?php

namespace CodebarAg\DocuWare\DTO\DocumentIndex;

class IndexTextDTO
{
    public function __construct(
        public string $name,
        public ?string $value,
    ) {

    }

    public static function make(string $name, ?string $value): self
    {
        return new self($name, $value);
    }

    public function values(): array
    {
        return [
            'FieldName' => $this->name,
            'Item' => $this->value,
            'ItemElementName' => 'String',
        ];
    }
}
