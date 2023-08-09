<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class UnableToLoginNoCookies extends RuntimeException
{
    public static function create(): self
    {
        return new self('Login failed to retrieve/return cookies.');
    }
}
