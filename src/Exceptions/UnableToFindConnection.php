<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class UnableToFindConnection extends RuntimeException
{
    public static function create(): self
    {
        return new self('Invalid connection set within your configuration "docuware.connection"');
    }
}
