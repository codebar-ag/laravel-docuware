<?php

namespace CodebarAg\DocuWare\Support;

use CodebarAg\DocuWare\Exceptions\UnableToFindPassphrase;

class EnsureValidPassphrase
{
    public static function check(): void
    {
        throw_if(
            empty(config('docuware.passphrase')),
            UnableToFindPassphrase::create(),
        );
    }
}
