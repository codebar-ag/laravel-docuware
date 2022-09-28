<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

class UnableToFindPassphrase extends RuntimeException
{
    public static function create(): self
    {
        return new static('Your passphrase is not found. Try to add "DOCUWARE_PASSPHRASE=passphrase" in your .env-file.');
    }
}
