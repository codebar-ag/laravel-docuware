<?php

namespace CodebarAg\DocuWare\DTO\DocumentIndex;

use Illuminate\Support\Carbon;

class IndexDateTimeDTO
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

    public static function makeWithFallback($name, object $value): mixed
    {
        return match (true) {
            $value instanceof Carbon => self::make($name, $value),
            default => null,
        };
    }

    public function values(): array
    {
        return [
            'FieldName' => $this->name,
            'Item' => $this->value->toDateTimeString(),
            'ItemElementName' => 'String',
        ];
    }
}
