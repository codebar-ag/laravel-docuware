<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Root of the single 2.0 exception hierarchy. Every DocuWare failure throws this (or a
 * subclass) carrying structurally-redacted context — instance, HTTP status, the DocuWare
 * message, and a correlation id — never a secret or a raw response body.
 */
class DocuWareException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $statusCode = null,
        public readonly ?string $instance = null,
        public readonly ?string $docuwareMessage = null,
        public readonly ?string $requestId = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode ?? 0, $previous);
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'instance' => $this->instance,
            'status' => $this->statusCode,
            'docuware_message' => $this->docuwareMessage,
            'request_id' => $this->requestId,
        ];
    }
}
