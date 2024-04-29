<?php

namespace CodebarAg\DocuWare\Connectors;

use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;

class DocuWareConnector extends Connector
{
    public function __construct(
        public readonly ?string $token = null,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function resolveBaseUrl(): string
    {
        return config('laravel-docuware.credentials.url').'/DocuWare/Platform';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->token);
    }
}
