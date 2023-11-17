<?php

namespace CodebarAg\DocuWare\Support;

use CodebarAg\DocuWare\Exceptions\UnableToFindPassphrase;

class EnsureValidPassphrase
{
    public static function check(): void
    {
        throw_if(
            empty(config('laravel-docuware.passphrase')),
            UnableToFindPassphrase::create(),
        );
    }
}
