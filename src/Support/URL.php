<?php

namespace CodebarAg\DocuWare\Support;

use Illuminate\Support\Str;

class URL
{
    /**
     * We have to apply the Base64URL encoding and replace plus (+) with
     * minus (-) and slash (/) with underscore (_). In addition DocuWare
     * removes the trailing ‘=’ characters and adds 0, 1 or 2 depending
     * on characters removed.
     *
     * Source: https://help.docuware.com/#/home/60284/2/2
     */
    public static function format(string $string): string
    {
        $padding = Str::substrCount($string, '=');

        return Str::of($string)
            ->replace('+', '-')
            ->replace('/', '_')
            ->replace('=', '')
            ->append($padding)
            ->__toString();
    }

    public static function formatWithBase64(string $string): string
    {
        return self::format(base64_encode($string));
    }
}
