<?php

namespace CodebarAg\DocuWare\DTO\DocumentIndex;

use Illuminate\Support\Collection;

class PrepareDTO
{
    public static function guess(string $name, mixed $value): mixed
    {
        $type = gettype($value);

        return match ($type) {
            'integer' => IndexNumericDTO::make($name, $value),
            'string' => IndexTextDTO::make($name, $value),
            'double' => IndexDecimalDTO::make($name, $value),
            'object' => IndexDateDTO::makeWithFallback($name, $value),
            default => null,
        };
    }

    public static function makeFields(Collection $indexes): array
    {
        return [
            'Fields' => $indexes
                ->map(fn (IndexTextDTO|IndexDateDTO|IndexDateTimeDTO|IndexNumericDTO|IndexDecimalDTO|IndexTableDTO $index) => $index->values())
                ->filter()
                ->values(),
        ];
    }

    public static function makeField(Collection $indexes, bool $forceUpdate = false): array
    {
        return [
            'Field' => $indexes
                ->map(fn (IndexTextDTO|IndexDateDTO|IndexDateTimeDTO|IndexNumericDTO|IndexDecimalDTO|IndexTableDTO $index) => $index->values())
                ->filter()
                ->values(),
            'ForceUpdate' => $forceUpdate,
        ];
    }
}
