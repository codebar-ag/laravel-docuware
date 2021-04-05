<?php

namespace CodebarAg\DocuWare\Support;

use CodebarAg\DocuWare\Exceptions\UnableToMakeRequest;
use CodebarAg\DocuWare\Exceptions\UnableToProcessRequest;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as Status;

class EnsureValidResponse
{
    public static function from(Response $response): void
    {
        throw_if(
            $response->status() === Status::HTTP_UNAUTHORIZED,
            UnableToMakeRequest::create(),
        );

        throw_if(
            $response->status() === Status::HTTP_UNPROCESSABLE_ENTITY,
            UnableToProcessRequest::create($response),
        );
    }
}
