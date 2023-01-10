<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class UnableToFindUrlCredential extends RuntimeException
{
    public static function create(): self
    {
        return new self('Your URL is not found. Try to add "DOCUWARE_URL=https://domain.docuware.cloud" in your .env-file.');
    }
}
