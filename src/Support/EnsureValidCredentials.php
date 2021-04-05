<?php

namespace CodebarAg\DocuWare\Support;

use CodebarAg\DocuWare\Exceptions\UnableToFindPasswordCredential;
use CodebarAg\DocuWare\Exceptions\UnableToFindUrlCredential;
use CodebarAg\DocuWare\Exceptions\UnableToFindUserCredential;

class EnsureValidCredentials
{
    public static function check(): void
    {
        throw_if(
            empty(config('docuware.url')),
            UnableToFindUrlCredential::create(),
        );

        throw_if(
            empty(config('docuware.user')),
            UnableToFindUserCredential::create(),
        );

        throw_if(
            empty(config('docuware.password')),
            UnableToFindPasswordCredential::create(),
        );
    }
}
