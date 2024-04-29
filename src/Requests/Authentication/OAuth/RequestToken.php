<?php

namespace CodebarAg\DocuWare\Requests\Authentication\OAuth;

use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken as RequestTokenDto;
use CodebarAg\DocuWare\Responses\Authentication\OAuth\RequestTokenResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasFormBody;

class RequestToken extends SoloRequest implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly mixed $tokenEndpoint,
        public readonly ?string $username = '',
        public readonly ?string $password = '',
        public readonly string $grantType = 'password',
        public readonly string $clientId = 'docuware.platform.net.client',
    ) {
    }

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
            'scope' => 'docuware.platform',
            'grant_type' => $this->grantType,
            'client_id' => $this->clientId,
            'username' => filled($this->username) ? $this->username : config('laravel-docuware.credentials.username'),
            'password' => filled($this->password) ? $this->password : config('laravel-docuware.credentials.password'),
        ];
    }

    public function createDtoFromResponse(Response $response): RequestTokenDto
    {
        return RequestTokenResponse::fromResponse($response);
    }
}
