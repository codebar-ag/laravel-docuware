<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class UnableToFindPasswordCredential extends RuntimeException
{
    public static function create(): self
    {
        return new self('Your password is not found. Try to add "DOCUWARE_PASSWORD=password" in your .env-file.');
    }
}
