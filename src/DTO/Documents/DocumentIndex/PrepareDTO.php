<?php

namespace CodebarAg\DocuWare\DTO\Documents\DocumentIndex;

use Illuminate\Support\Collection;

class PrepareDTO
{
    /**
     * @param  Collection<int, IndexTextDTO|IndexDateDTO|IndexDateTimeDTO|IndexNumericDTO|IndexDecimalDTO|IndexTableDTO|IndexKeywordDTO|IndexMemoDTO>  $indexes
     * @return array<string, mixed>
     */
    public static function makeFields(Collection $indexes): array
    {
        return [
            'Fields' => $indexes
                ->map(fn (IndexTextDTO|IndexDateDTO|IndexDateTimeDTO|IndexNumericDTO|IndexDecimalDTO|IndexTableDTO|IndexKeywordDTO|IndexMemoDTO $index) => $index->values())
                ->filter()
                ->values(),
        ];
    }

    /**
     * @param  Collection<int, IndexTextDTO|IndexDateDTO|IndexDateTimeDTO|IndexNumericDTO|IndexDecimalDTO|IndexTableDTO|IndexKeywordDTO|IndexMemoDTO>  $indexes
     * @return array<string, mixed>
     */
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
