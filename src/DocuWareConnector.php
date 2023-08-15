<?php

namespace CodebarAg\DocuWare;

use CodebarAg\DocuWare\Support\Auth;
use CodebarAg\DocuWare\Support\EnsureValidCredentials;
use Saloon\Http\Connector;

class DocuWareConnector extends Connector
{
    public function __construct()
    {
        EnsureValidCredentials::check();
    }

    /**
     * @throws \Exception
     */
    public function resolveBaseUrl(): string
    {
        return config('docuware.credentials.url').'/DocuWare/Platform';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    public function defaultConfig(): array
    {
        return [
            'timeout' => config('docuware.timeout'),
            'cookies' => Auth::cookieJar(),
        ];
    }
}
