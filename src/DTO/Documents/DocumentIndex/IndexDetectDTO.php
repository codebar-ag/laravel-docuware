<?php

namespace CodebarAg\DocuWare\DTO\Documents\DocumentIndex;

use Illuminate\Support\Carbon;

class IndexDetectDTO
{
    public function __construct(
        public string $name,
        public mixed $value,
    ) {}

    public static function make(string $name, mixed $value)
    {
        return match (true) {
            is_string($value) => IndexTextDTO::make($name, $value),
            is_int($value) => IndexNumericDTO::make($name, $value),
            is_float($value) => IndexDecimalDTO::make($name, $value),
            $value instanceof Carbon => IndexDateTimeDTO::make($name, $value),
            $value instanceof \Carbon\Carbon => IndexDateTimeDTO::make($name, $value),
            default => null,
        };
    }
}
