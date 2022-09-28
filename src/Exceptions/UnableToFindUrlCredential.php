<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

class UnableToFindUrlCredential extends RuntimeException
{
    public static function create(): self
    {
        return new static('Your URL is not found. Try to add "DOCUWARE_URL=https://domain.docuware.cloud" in your .env-file.');
    }
}
