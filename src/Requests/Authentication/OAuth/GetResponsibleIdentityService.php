<?php

namespace CodebarAg\DocuWare\Requests\Authentication\OAuth;

use CodebarAg\DocuWare\DTO\Authentication\OAuth\ResponsibleIdentityService;
use CodebarAg\DocuWare\Responses\Authentication\OAuth\GetResponsibleIdentityServiceResponse;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Http\SoloRequest;

class GetResponsibleIdentityService extends SoloRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        $base = config('laravel-docuware.credentials.url').'/DocuWare/Platform';

        return $base.'/Home/IdentityServiceInfo';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    public function createDtoFromResponse(Response $response): ResponsibleIdentityService
    {
        return GetResponsibleIdentityServiceResponse::fromResponse($response);
    }
}
