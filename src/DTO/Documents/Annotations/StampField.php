<?php

namespace CodebarAg\DocuWare\DTO\Documents\Annotations;

/**
 * A single fill-in field of a stamp (the `<#1>` placeholders DocuWare stamps expose).
 */
final class StampField
{
    public function __construct(
        public string $name,
        public string $value,
    ) {}

    public static function make(string $name, string $value): self
    {
        return new self($name, $value);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'Name' => $this->name,
            'TypedValue' => [
                'Item' => $this->value,
                'ItemElementName' => 'string',
            ],
            'Value' => $this->value,
            'TextAsString' => $this->value,
        ];
    }
}
