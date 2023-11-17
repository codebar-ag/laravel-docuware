<?php

namespace CodebarAg\DocuWare\DTO\DocumentIndex;

use Illuminate\Support\Collection;

class PrepareIndex
{
    public function __construct()
    {

    }

    public static function make(string $name, mixed $value): mixed
    {
        $type = gettype($value);

        return match ($type) {
            'integer' => IndexNumeric::make($name, $value),
            'string' => IndexText::make($name, $value),
            'double' => IndexDecimal::make($name, $value),
            //todo check
            'carbon' => IndexDate::make($name, $value),
            default => null,
        };
    }

    public static function makeContent(Collection $indexes): string
    {
        $indexContent = (object) [
            'Fields' => $indexes
                ->map(fn (IndexText|IndexDate|IndexNumeric|IndexDecimal|IndexTable $index) => $index->values())
                ->filter()
                ->values()
                ->toArray(),
        ];

        return json_encode($indexContent);
    }
}
