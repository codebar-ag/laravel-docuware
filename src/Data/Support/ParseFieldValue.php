<?php

namespace CodebarAg\DocuWare\Data\Support;

use Carbon\Carbon;
use CodebarAg\DocuWare\Data\Documents\TableRowData;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Parses a DocuWare index-field value into its native PHP type, mirroring the wire contract:
 * Int/String/Decimal/Date(Time)/Keywords/Table. Table values resolve to a collection of
 * {@see TableRowData}. Scalar/date parsing is shared with {@see ParseValue} so behaviour stays
 * identical to the validated v1 mapping.
 */
final class ParseFieldValue
{
    /**
     * @param  array<string, mixed>|null  $field
     * @return null|int|float|Carbon|string|Collection<int, TableRowData>
     */
    public static function field(?array $field): null|int|float|Carbon|string|Collection
    {
        if (! $field || Arr::get($field, 'IsNull')) {
            return null;
        }

        $item = Arr::get($field, 'Item');
        $itemElementName = Arr::get($field, 'ItemElementName');

        return match ($itemElementName) {
            'Int' => (int) $item,
            'String' => (string) $item,
            'Decimal' => (float) $item,
            'Date', 'DateTime' => is_string($item) ? ParseValue::date($item) : null,
            'Keywords' => Arr::join(
                match (true) {
                    is_array($item) && is_array($k = Arr::get($item, 'Keyword', [])) => $k,
                    default => [],
                },
                ', '
            ),
            'Table' => is_array($item) ? self::table($item) : null,
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $item
     * @return Collection<int, TableRowData>|null
     */
    private static function table(array $item): ?Collection
    {
        if (Arr::get($item, '$type') !== 'DocumentIndexFieldTable') {
            return null;
        }

        $rows = Arr::get($item, 'Row');
        if (! is_array($rows)) {
            return null;
        }

        return collect(array_values($rows))
            ->filter(fn ($row) => is_array($row))
            ->map(function (array $row) {
                $columnValue = Arr::get($row, 'ColumnValue', []);

                return TableRowData::fromDocuWare(is_array($columnValue) ? $columnValue : []);
            })
            ->values();
    }
}
