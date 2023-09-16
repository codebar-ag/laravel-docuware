<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class UnableToMakeRequest extends RuntimeException
{
    public static function create(): self
    {
        return new self(
            'You are not authorized. Please check your credentials and try again.',
        );
    }
}
