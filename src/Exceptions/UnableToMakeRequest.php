<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

class UnableToMakeRequest extends RuntimeException
{
    public static function create(): self
    {
        return new static(
            'You are not authorized. '.
            'Make sure you are logged in with the correct credentials.'.
            'Try to use "->login()" before you make other requests.',
        );
    }
}
