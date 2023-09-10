<?php

namespace CodebarAg\DocuWare\Support;

use CodebarAg\DocuWare\Exceptions\UnableToMakeRequest;
use CodebarAg\DocuWare\Exceptions\UnableToProcessRequest;
use Illuminate\Http\Client\Response;
use Saloon\Contracts\Response as SaloonContracts;
use Saloon\Http\Response as SaloonResponse;
use Symfony\Component\HttpFoundation\Response as Status;

class EnsureValidResponse
{
    public static function from(Response|SaloonResponse|SaloonContracts $response): void
    {
        if ($response->successful()) {
            return;
        }

        throw_if(
            $response->status() === Status::HTTP_UNAUTHORIZED,
            UnableToMakeRequest::create(),
        );

        if (! $response->json('Message')) {
            return;
        }

        throw_if(
            in_array($response->status(), [
                Status::HTTP_NOT_FOUND,
                Status::HTTP_UNPROCESSABLE_ENTITY,
                Status::HTTP_INTERNAL_SERVER_ERROR,
            ]),
            UnableToProcessRequest::create($response),
        );
    }
}
