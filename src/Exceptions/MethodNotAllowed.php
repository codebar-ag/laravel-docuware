<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class MethodNotAllowed extends RuntimeException
{
    public static function create(): self
    {
        return new self('This method is not allowed on connection '.config('docuware.connection').'.');
    }
}
