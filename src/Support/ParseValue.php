<?php

namespace CodebarAg\DocuWare\Support;

use Carbon\Carbon;
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
        null | int | float | Carbon | string $default = null,
    ): null | int | float | Carbon | string {
        if (! $field) {
            return $default;
        }

        if ($field['IsNull']) {
            return $default;
        }

        return match ($field['ItemElementName']) {
            'Int' => (int) $field['Item'],
            'Decimal' => (float) $field['Item'],
            'Date','DateTime' => self::date($field['Item']),
            'Keywords' => implode(', ', $field['Item']['Keyword']),
            default => (string) $field['Item'],
        };
    }
}
