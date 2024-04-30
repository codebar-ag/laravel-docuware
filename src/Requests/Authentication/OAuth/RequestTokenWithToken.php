<?php

namespace CodebarAg\DocuWare\Requests\Authentication\OAuth;

use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken as RequestTokenDto;
use CodebarAg\DocuWare\Responses\Authentication\OAuth\RequestTokenResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasFormBody;

class RequestTokenWithToken extends SoloRequest implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly mixed $tokenEndpoint,
        public readonly ?string $token = '',
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
            'grant_type' => 'dwtoken',
            'client_id' => $this->clientId,
            'token' => $this->token,
        ];
    }

    public function createDtoFromResponse(Response $response): RequestTokenDto
    {
        return RequestTokenResponse::fromResponse($response);
    }
}
