<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

class UnableToMakeRequest extends RuntimeException
{
    public static function create(): self
    {
        throw new self(
            'You are not authorized. Make sure you are logged in.' .
                'Try to use `login()` before you make other requests.'
        );
    }
}
