<?php

namespace CodebarAg\DocuWare\Requests\Authentication\OAuth;

use CodebarAg\DocuWare\DTO\Authentication\OAuth\IdentityServiceConfiguration;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Http\SoloRequest;

class GetIdentityServiceConfiguration extends SoloRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public string $identityServiceUrl,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return $this->identityServiceUrl.'/.well-known/openid-configuration';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    public function createDtoFromResponse(Response $response): IdentityServiceConfiguration
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return IdentityServiceConfiguration::make($response->json());
    }
}
