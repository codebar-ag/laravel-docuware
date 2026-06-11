<?php

namespace CodebarAg\DocuWare\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ParseValue
{
    /**
     * Parse a DocuWare `/Date(ms)/` timestamp (milliseconds) to a Carbon instance.
     */
    public static function date(string $date): Carbon
    {
        $timestamp = Str::of($date)
            ->ltrim('/Date(')
            ->rtrim(')/')
            ->__toString();

        return Carbon::createFromTimestampMs($timestamp);
    }

    /**
     * Null-safe variant of {@see self::date()} — a missing/empty value yields null instead of a
     * `TypeError`, matching DocuWare's contract where date fields are optional.
     */
    public static function dateOrNull(?string $date): ?Carbon
    {
        return $date === null || $date === '' ? null : self::date($date);
    }

    /**
     * Parse a DocuWare `/Date(seconds)/` timestamp to a Carbon instance. A handful of endpoints
     * (sections, workflow history) emit seconds rather than milliseconds — this preserves that
     * validated v1 behaviour while centralising the parsing.
     */
    public static function dateInSeconds(string $date): Carbon
    {
        $timestamp = Str::of($date)
            ->ltrim('/Date(')
            ->rtrim(')/')
            ->__toString();

        return Carbon::createFromTimestamp($timestamp);
    }

    /**
     * Null-safe variant of {@see self::dateInSeconds()}.
     */
    public static function dateInSecondsOrNull(?string $date): ?Carbon
    {
        return $date === null || $date === '' ? null : self::dateInSeconds($date);
    }
}
