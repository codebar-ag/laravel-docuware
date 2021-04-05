<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

class UnableToFindCredentials extends RuntimeException
{
    public static function url(): self
    {
        return new static(
            'The DocuWare-URL is not found. ' .
                'Try to add following in the .env-file: ' .
                '"DOCUWARE_URL=https://domain.docuware.cloud"'
        );
    }

    public static function user(): self
    {
        return new static(
            'The DocuWare-User is not found. ' .
                'Try to add following in the .env-file: ' .
                '"DOCUWARE_USER=user@domain.test"'
        );
    }

    public static function password(): self
    {
        return new static(
            'The DocuWare-Password is not found. ' .
                'Try to add following in the .env-file: ' .
                '"DOCUWARE_PASSWORD=password"'
        );
    }
}
