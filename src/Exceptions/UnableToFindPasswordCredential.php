<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

class UnableToFindPasswordCredential extends RuntimeException
{
    public static function create(): self
    {
        return new static('Your password is not found. Try to add "DOCUWARE_PASSWORD=password" in your .env-file.');
    }
}
