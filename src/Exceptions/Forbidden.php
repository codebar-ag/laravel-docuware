<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class Forbidden extends RuntimeException
{
    public static function make(?string $message, int $status = 403): self
    {
        return new self(
            $message !== null && $message !== ''
                ? $message
                : 'You do not have permission to perform this action in DocuWare.',
            $status,
        );
    }
}
