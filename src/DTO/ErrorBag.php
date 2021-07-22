<?php

namespace CodebarAg\DocuWare\DTO;

use Exception;

class ErrorBag
{
    public function __construct(
        public int $code,
        public string $message,
    ) {
    }

    public static function make(Exception $e): self
    {
        return new self(
            code: (int) $e->getCode(),
            message: $e->getMessage(),
        );
    }
}
