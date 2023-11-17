<?php

namespace CodebarAg\DocuWare\DTO\DocumentIndex;

use Carbon\Carbon;

class IndexDate
{
    public function __construct(
        public string $name,
        public Carbon $value,
    ) {

    }

    public static function make(string $name, Carbon $value): self
    {
        return new self($name, $value);
    }

    public function values(): array
    {
        return [
            'FieldName' => $this->name,
            'Item' => $this->value->toDateString(),
            'ItemElementName' => 'Decimal',
        ];
    }
}
