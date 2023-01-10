<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class UnableToLogin extends RuntimeException
{
    public static function create(): self
    {
        return new self('Ensure your credentials are correct.');
    }
}
