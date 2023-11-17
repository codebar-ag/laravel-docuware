<?php

namespace CodebarAg\DocuWare\DTO\DocumentIndex;

class PrepareIndexTable
{
    public function __construct()
    {

    }

    public static function make(string $name, mixed $value): mixed
    {
        $type = gettype($value);

        return match ($type) {
            'string' => IndexText::make($name, $value),
            'integer' => IndexDecimal::make($name, $value),
            'double' => IndexDecimal::make($name, $value),
            //todo check
            'carbon' => IndexDate::make($name, $value),
            default => null,
        };
    }
}
