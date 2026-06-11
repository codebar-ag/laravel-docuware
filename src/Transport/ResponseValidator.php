<?php

namespace CodebarAg\DocuWare\Transport;

use CodebarAg\DocuWare\Exceptions\AuthenticationException;
use CodebarAg\DocuWare\Exceptions\BadRequestException;
use CodebarAg\DocuWare\Exceptions\ConflictException;
use CodebarAg\DocuWare\Exceptions\DocuWareException;
use CodebarAg\DocuWare\Exceptions\ForbiddenException;
use CodebarAg\DocuWare\Exceptions\MethodNotAllowedException;
use CodebarAg\DocuWare\Exceptions\NotFoundException;
use CodebarAg\DocuWare\Exceptions\RateLimitException;
use CodebarAg\DocuWare\Exceptions\RequestException;
use CodebarAg\DocuWare\Exceptions\ValidationException;
use CodebarAg\DocuWare\Security\Redactor;
use Illuminate\Support\Str;
use Saloon\Http\Response;

/**
 * Maps a non-2xx response to the unified {@see DocuWareException} hierarchy, attaching
 * redacted context (instance, status, DocuWare message, correlation id). The single throw
 * point for the resource layer.
 */
final class ResponseValidator
{
    public static function validate(Response $response, ?string $instance = null): void
    {
        if ($response->successful()) {
            return;
        }

        $status = $response->status();
        $message = self::extractMessage($response);
        $requestId = self::requestId($response);
        $summary = "DocuWare request to instance [{$instance}] failed with HTTP {$status}"
            .($message !== null ? ": {$message}" : '.');

        throw match ($status) {
            400 => new BadRequestException($summary, $status, $instance, $message, $requestId),
            401 => new AuthenticationException($summary, $status, $instance, $message, $requestId),
            403 => new ForbiddenException($summary, $status, $instance, $message, $requestId),
            404 => new NotFoundException($summary, $status, $instance, $message, $requestId),
            405 => new MethodNotAllowedException($summary, $status, $instance, $message, $requestId),
            409 => new ConflictException($summary, $status, $instance, $message, $requestId),
            422 => new ValidationException($summary, $status, $instance, $message, $requestId),
            429 => new RateLimitException($summary, $status, $instance, $message, $requestId),
            default => $status >= 400 && $status < 500
                ? new RequestException($summary, $status, $instance, $message, $requestId)
                : new DocuWareException($summary, $status, $instance, $message, $requestId),
        };
    }

    /**
     * Best-effort, redacted message extraction tolerant of JSON, OAuth-style and HTML bodies.
     */
    private static function extractMessage(Response $response): ?string
    {
        $body = (string) $response->body();
        if ($body === '') {
            return null;
        }

        $redactor = new Redactor;

        /** @var mixed $decoded */
        $decoded = json_decode($body, true);
        if (is_array($decoded)) {
            foreach (['Message', 'error_description', 'error'] as $key) {
                $value = $decoded[$key] ?? null;
                if (is_string($value) && $value !== '') {
                    return $redactor->string($value);
                }
            }

            return null;
        }

        $summary = trim((string) preg_replace('/\s\s+/', ' ', strip_tags($body)));

        return $summary !== '' ? $redactor->string(Str::limit($summary, 300)) : null;
    }

    private static function requestId(Response $response): ?string
    {
        foreach (['X-Request-Id', 'Request-Id', 'request-context'] as $header) {
            $value = $response->header($header);
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }
}
