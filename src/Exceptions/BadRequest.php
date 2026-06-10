<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class BadRequest extends RuntimeException
{
    public static function make(?string $message, int $status = 400): self
    {
        return new self(
            $message !== null && $message !== ''
                ? $message
                : 'The request was rejected by DocuWare (bad request).',
            $status,
        );
    }
}
