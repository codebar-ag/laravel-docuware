<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

class UnableToLogin extends RuntimeException
{
    public static function create(): self
    {
        return new static('Ensure your credentials are correct.');
    }
}
