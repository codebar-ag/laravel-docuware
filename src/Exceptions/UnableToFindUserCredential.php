<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

class UnableToFindUserCredential extends RuntimeException
{
    public static function create(): self
    {
        return new static('Your username is not found.ry to add "DOCUWARE_USERNAME=user@domain.test" following in your .env-file.');
    }
}
