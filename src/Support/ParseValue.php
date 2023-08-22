<?php

namespace CodebarAg\DocuWare\Support;

use Carbon\Carbon;
use CodebarAg\DocuWare\DTO\TableRow;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ParseValue
{
    public static function field(
        ?array $field,
        int|float|Carbon|string|Collection $default = null,
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
            'Date', 'DateTime' => self::date($item),
            'Keywords' => Arr::join($item['Keyword'], ', '),
            'Table' => self::table($item),
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

    public static function table(array $Item): ?Collection
    {
        return match ($Item['$type']) {
            'DocumentIndexFieldTable' => self::documentIndexFieldTable($Item['Row']),
            default => null,
        };
    }

    public static function documentIndexFieldTable(array $Row): ?Collection
    {
        $rows = collect($Row);

        return $rows->map(function (array $row) {
            return TableRow::fromJson($row['ColumnValue']);
        });
    }
}
