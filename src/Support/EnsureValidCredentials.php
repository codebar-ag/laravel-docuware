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
            empty(config('laravel-docuware.credentials.url')),
            UnableToFindUrlCredential::create(),
        );

        throw_if(
            empty(config('laravel-docuware.credentials.username')),
            UnableToFindUserCredential::create(),
        );

        throw_if(
            empty(config('laravel-docuware.credentials.password')),
            UnableToFindPasswordCredential::create(),
        );

    }
}
