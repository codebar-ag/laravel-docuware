<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class UnableToFindUserCredential extends RuntimeException
{
    public static function create(): self
    {
        return new self('Your username is not found.ry to add "DOCUWARE_USERNAME=user@domain.test" following in your .env-file.');
    }
}
