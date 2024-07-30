<?php

namespace CodebarAg\DocuWare\Requests\Authentication\OAuth;

use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken as RequestTokenDto;
use CodebarAg\DocuWare\Responses\Authentication\OAuth\RequestTokenResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasFormBody;

class RequestTokenWithCredentialsTrustedUser extends SoloRequest implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly mixed $tokenEndpoint,
        public readonly string $clientId = 'docuware.platform.net.client',
        public readonly string $scope = 'docuware.platform',
        public readonly string $username = '',
        public readonly string $password = '',
        public readonly string $impersonateName = '',
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

    public function defaultBody(): array
    {
        return [
            'grant_type' => 'trusted',
            'scope' => $this->scope,
            'client_id' => $this->clientId,
            'username' => $this->username,
            'password' => $this->password,
            'impersonateName' => $this->impersonateName,
        ];
    }

    public function createDtoFromResponse(Response $response): RequestTokenDto
    {
        return RequestTokenResponse::fromResponse($response);
    }
}
