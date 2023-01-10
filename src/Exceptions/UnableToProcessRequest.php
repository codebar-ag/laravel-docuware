<?php

namespace CodebarAg\DocuWare\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;

final class UnableToProcessRequest extends RuntimeException
{
    public static function create(Response $response): self
    {
        return new self($response->json('Message'), $response->status());
    }
}
