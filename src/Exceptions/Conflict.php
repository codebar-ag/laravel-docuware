<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class Conflict extends RuntimeException
{
    public static function make(?string $message, int $status = 409): self
    {
        return new self(
            $message !== null && $message !== ''
                ? $message
                : 'The request conflicts with the current state of the DocuWare resource.',
            $status,
        );
    }
}
