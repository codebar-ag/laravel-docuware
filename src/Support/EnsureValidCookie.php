<?php

namespace CodebarAg\DocuWare\Support;

use CodebarAg\DocuWare\DocuWare;

class EnsureValidCookie
{
    public static function check(): void
    {
        if (Auth::check()) {
            return;
        }

        (new DocuWare())->login();
    }
}
