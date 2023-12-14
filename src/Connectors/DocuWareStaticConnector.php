<?php

namespace CodebarAg\DocuWare\Connectors;

use CodebarAg\DocuWare\Support\Auth;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use CodebarAg\DocuWare\Support\EnsureValidCredentials;
use GuzzleHttp\Cookie\CookieJar;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\OffsetPaginator;

class DocuWareStaticConnector extends Connector implements HasPagination
{
    public CookieJar $cookieJar;

    public function __construct()
    {
        EnsureValidCredentials::check();
        EnsureValidCookie::check();

        $this->cookieJar = Auth::cookieJar() ?? throw new \Exception('No cookie jar found');
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

    public function defaultConfig(): array
    {
        return [
            'timeout' => config('laravel-docuware.timeout'),
            'cookies' => $this->cookieJar,
        ];
    }

    public function getCoookieJar(): CookieJar
    {
        return $this->cookieJar;
    }

    public function paginate(Request $request): OffsetPaginator
    {
        return new DocuWareOffsetPaginator(connector: $this, request: $request);
    }
}
