<?php

namespace CodebarAg\DocuWare\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;
use Saloon\Http\Response as SaloonResponse;

final class UnableToProcessRequest extends RuntimeException
{
    public static function create(Response|SaloonResponse $response): self
    {
        return new self($response->json('Message'), $response->status());
    }
}
