<?php

namespace CodebarAg\DocuWare\DTO\Documents\DocumentIndex;

use Illuminate\Support\Collection;

class PrepareDTO
{
    public static function makeFields(Collection $indexes): array
    {
        return [
            'Fields' => $indexes
                ->map(fn (IndexTextDTO|IndexDateDTO|IndexDateTimeDTO|IndexNumericDTO|IndexDecimalDTO|IndexTableDTO|IndexKeywordDTO|IndexMemoDTO $index) => $index->values())
                ->filter()
                ->values(),
        ];
    }

    public static function makeField(Collection $indexes, bool $forceUpdate = false): array
    {
        return [
            'Field' => $indexes
                ->map(fn (IndexTextDTO|IndexDateDTO|IndexDateTimeDTO|IndexNumericDTO|IndexDecimalDTO|IndexTableDTO|IndexKeywordDTO|IndexMemoDTO $index) => $index->values())
                ->filter()
                ->values(),
            'ForceUpdate' => $forceUpdate,
        ];
    }
}
