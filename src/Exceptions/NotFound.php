<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class NotFound extends RuntimeException
{
    public static function make(?string $message, int $status = 404): self
    {
        return new self(
            $message !== null && $message !== ''
                ? $message
                : 'The requested DocuWare resource could not be found.',
            $status,
        );
    }
}
