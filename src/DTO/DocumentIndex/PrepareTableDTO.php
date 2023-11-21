<?php

namespace CodebarAg\DocuWare\DTO\DocumentIndex;

class PrepareTableDTO
{
    public static function make(string $name, mixed $value): mixed
    {
        $type = gettype($value);

        return match ($type) {
            'string' => IndexTextDTO::make($name, $value),
            'integer' => IndexDecimalDTO::make($name, $value),
            'double' => IndexDecimalDTO::make($name, $value),
            'object' => IndexDateDTO::makeWithFallback($name, $value),
            default => null,
        };
    }
}
