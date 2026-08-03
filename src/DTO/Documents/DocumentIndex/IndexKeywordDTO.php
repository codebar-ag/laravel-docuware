<?php

namespace CodebarAg\DocuWare\DTO\Documents\DocumentIndex;

class IndexKeywordDTO
{
    /**
     * @param  list<string>  $values
     */
    public function __construct(
        public string $name,
        public array $values,
    ) {}

    /**
     * @param  list<string>  $values
     */
    public static function make(string $name, array $values): self
    {
        return new self($name, $values);
    }

    /**
     * @return array<string, mixed>
     */
    public function values(): array
    {
        return [
            'FieldName' => $this->name,
            'Keywords' => $this->values,
            'ItemElementName' => 'Keywords',
        ];
    }
}
