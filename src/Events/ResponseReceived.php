<?php

namespace CodebarAg\DocuWare\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Fired after every DocuWare HTTP response with a structurally-redacted snapshot — method,
 * path, status, duration and request id. The raw body is only present when
 * `docuware.debug.capture_bodies` is enabled, and is redacted even then.
 *
 * Replaces the legacy `DocuWareResponseLog`, which carried the entire raw `Response`
 * (including auth bodies) — a secret-leak vector.
 */
final class ResponseReceived
{
    use Dispatchable;

    /**
     * @param  array<string, mixed>|null  $headers
     */
    public function __construct(
        public string $instance,
        public string $method,
        public string $uri,
        public int $status,
        public ?float $durationMs = null,
        public ?string $requestId = null,
        public ?array $headers = null,
        public ?string $body = null,
    ) {}
}
