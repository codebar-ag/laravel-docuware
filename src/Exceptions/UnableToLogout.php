<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class UnableToLogout extends RuntimeException
{
    public static function create(): self
    {
        return new self('Cannot logout due to setting config `docuware.cookies`.');
    }
}
