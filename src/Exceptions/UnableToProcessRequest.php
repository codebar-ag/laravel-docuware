<?php

namespace CodebarAg\DocuWare\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;
use Saloon\Http\Response as SaloonResponse;

final class UnableToProcessRequest extends RuntimeException
{
    /**
     * Kept for backward compatibility. Prefer {@see self::make()} which never throws on
     * non-JSON error bodies.
     */
    public static function create(Response|SaloonResponse $response): self
    {
        return self::make($response->status(), self::messageFromResponse($response));
    }

    public static function make(int $status, ?string $message): self
    {
        return new self(
            $message !== null && $message !== ''
                ? $message
                : 'DocuWare was unable to process the request.',
            $status,
        );
    }

    protected static function messageFromResponse(Response|SaloonResponse $response): ?string
    {
        $body = (string) $response->body();

        /** @var mixed $decoded */
        $decoded = json_decode($body, true);

        if (is_array($decoded) && isset($decoded['Message']) && is_string($decoded['Message'])) {
            return $decoded['Message'];
        }

        return null;
    }
}
