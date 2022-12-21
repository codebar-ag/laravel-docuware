<?php

namespace CodebarAg\DocuWare\Support;

use Carbon\Carbon;
use CodebarAg\DocuWare\DTO\TableRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ParseValue
{
    public static function date(string $date): Carbon
    {
        $timestamp = Str::of($date)
            ->ltrim('/Date(')
            ->rtrim(')/')
            ->__toString();

        return Carbon::createFromTimestampMs($timestamp);
    }

    public static function field(
        ?array $field,
        null|int|float|Carbon|string|Collection $default = null,
    ): null|int|float|Carbon|string|Collection {
        if (! $field) {
            return $default;
        }

        if ($field['IsNull']) {
            return $default;
        }

        return match ($field['ItemElementName']) {
            'Int' => (int) $field['Item'],
            'Decimal' => (float) $field['Item'],
            'Date', 'DateTime' => self::date($field['Item']),
            'Keywords' => implode(', ', $field['Item']['Keyword']),
            'Table' => self::table($field['Item']),
            default => (string) $field['Item'],
        };
    }

    public static function table(array $Item): Collection|null
    {
        return match ($Item['$type']) {
            'DocumentIndexFieldTable' => self::documentIndexFieldTable($Item['Row']),
            default => null,
        };
    }

    public static function documentIndexFieldTable(array $Row): Collection|null
    {
        $rows = collect($Row);

        return $rows->map(function (array $row) {
            return TableRow::fromJson($row['ColumnValue']);
        });
    }
}
