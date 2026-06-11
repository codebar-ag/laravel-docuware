<?php

namespace CodebarAg\DocuWare\Support;

use Carbon\Carbon;
use Illuminate\Support\Str;

class ParseValue
{
    /**
     * Parse a DocuWare `/Date(ms)/` timestamp to a Carbon instance.
     */
    public static function date(string $date): Carbon
    {
        $timestamp = Str::of($date)
            ->ltrim('/Date(')
            ->rtrim(')/')
            ->__toString();

        return Carbon::createFromTimestampMs($timestamp);
    }
}
