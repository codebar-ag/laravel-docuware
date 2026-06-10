<?php

namespace CodebarAg\DocuWare\Support;

use CodebarAg\DocuWare\Exceptions\BadRequest;
use CodebarAg\DocuWare\Exceptions\Conflict;
use CodebarAg\DocuWare\Exceptions\Forbidden;
use CodebarAg\DocuWare\Exceptions\NotFound;
use CodebarAg\DocuWare\Exceptions\UnableToMakeRequest;
use CodebarAg\DocuWare\Exceptions\UnableToProcessRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use Saloon\Http\Response as SaloonResponse;
use Symfony\Component\HttpFoundation\Response as Status;

class EnsureValidResponse
{
    /**
     * Throw a typed exception for any non-2xx response. Never returns silently on failure,
     * and never throws while trying to read a non-JSON error body.
     */
    public static function from(Response|SaloonResponse $response): void
    {
        if ($response->successful()) {
            return;
        }

        $status = $response->status();
        $message = self::extractMessage($response);

        throw match ($status) {
            Status::HTTP_BAD_REQUEST => BadRequest::make($message, $status),
            Status::HTTP_UNAUTHORIZED => UnableToMakeRequest::create(),
            Status::HTTP_FORBIDDEN => Forbidden::make($message, $status),
            Status::HTTP_NOT_FOUND => NotFound::make($message, $status),
            Status::HTTP_CONFLICT => Conflict::make($message, $status),
            default => UnableToProcessRequest::make($status, $message),
        };
    }

    /**
     * Best-effort message extraction that tolerates JSON, OAuth-style error bodies, and HTML.
     */
    protected static function extractMessage(Response|SaloonResponse $response): ?string
    {
        $body = (string) $response->body();

        if ($body === '') {
            return null;
        }

        /** @var mixed $decoded */
        $decoded = json_decode($body, true);

        if (is_array($decoded)) {
            foreach (['Message', 'error_description', 'error'] as $key) {
                if (isset($decoded[$key]) && is_string($decoded[$key]) && $decoded[$key] !== '') {
                    return $decoded[$key];
                }
            }

            return null;
        }

        // Non-JSON (e.g. HTML error page): return a short, single-line summary.
        $summary = trim((string) preg_replace('/\s\s+/', ' ', strip_tags($body)));

        return $summary !== '' ? Str::limit($summary, 300) : null;
    }
}
