<?php

namespace CodebarAg\DocuWare\Requests\Authentication\OAuth;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasFormBody;

/**
 * Exchange a DocuWare login token for an OAuth access token (`grant_type=dwtoken`).
 * Postman: "3.b Request Token w/ a DocuWare Token".
 */
class RequestTokenWithDocuWareToken extends SoloRequest implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly mixed $tokenEndpoint,
        public readonly string $token,
        public readonly string $clientId = 'docuware.platform.net.client',
        public readonly string $scope = 'docuware.platform',
    ) {}

    public function resolveEndpoint(): string
    {
        return $this->tokenEndpoint;
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultBody(): array
    {
        return [
            'grant_type' => 'dwtoken',
            'scope' => $this->scope,
            'client_id' => $this->clientId,
            'token' => $this->token,
        ];
    }
}
