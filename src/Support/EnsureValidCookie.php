<?php

namespace CodebarAg\DocuWare\Support;

use CodebarAg\DocuWare\DocuWare;
use Illuminate\Support\Facades\Cache;

class EnsureValidCookie
{
    public static function check(): void
    {
        if (Cache::has('docuware.cookies')) {
            return;
        }

        (new DocuWare())->login();
    }
}
