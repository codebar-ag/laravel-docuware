<?php

namespace CodebarAg\DocuWare\Support;

use Carbon\Carbon;
use CodebarAg\DocuWare\DTO\Documents\TableRow;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ParseValue
{
    /**
     * @param  array<string, mixed>|null  $field
     * @param  int|float|Carbon|string|Collection<int, mixed>|null  $default
     * @return int|float|Carbon|string|Collection<int, mixed>|null
     */
    public static function field(
        ?array $field,
        int|float|Carbon|string|Collection|null $default = null,
    ): null|int|float|Carbon|string|Collection {
        if (! $field || $field['IsNull']) {
            return $default;
        }

        $item = Arr::get($field, 'Item');
        $itemElementName = Arr::get($field, 'ItemElementName');

        return match ($itemElementName) {
            'Int' => (int) $item,
            'String' => (string) $item,
            'Decimal' => (float) $item,
            'Date', 'DateTime' => self::date(is_string($item) ? $item : ''),
            'Keywords' => Arr::join(
                is_array($item) && isset($item['Keyword']) && is_array($item['Keyword']) ? $item['Keyword'] : [],
                ', '
            ),
            'Table' => is_array($item) ? self::table($item) : $default,
            default => $default,
        };
    }

    public static function date(string $date): Carbon
    {
        $timestamp = Str::of($date)
            ->ltrim('/Date(')
            ->rtrim(')/')
            ->__toString();

        return Carbon::createFromTimestampMs($timestamp);
    }

    /**
     * @param  array<string, mixed>  $Item
     * @return Collection<int, TableRow>|null
     */
    public static function table(array $Item): ?Collection
    {
        $type = $Item['$type'] ?? null;

        return match ($type) {
            'DocumentIndexFieldTable' => isset($Item['Row']) && is_array($Item['Row'])
                ? self::documentIndexFieldTable($Item['Row'])
                : null,
            default => null,
        };
    }

    /**
     * @param  array<int|string, mixed>  $Row
     * @return Collection<int, TableRow>|null
     */
    public static function documentIndexFieldTable(array $Row): ?Collection
    {
        /** @var list<array<string, mixed>> $list */
        $list = [];
        foreach (array_values($Row) as $row) {
            if (is_array($row)) {
                $list[] = $row;
            }
        }

        $rows = collect($list);

        return $rows->map(function (array $row) {
            $columnValue = $row['ColumnValue'] ?? [];

            return TableRow::fromJson(is_array($columnValue) ? $columnValue : []);
        });
    }
}
