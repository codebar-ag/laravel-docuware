<?php

namespace CodebarAg\DocuWare\Connectors;

use CodebarAg\DocuWare\Support\Auth;
use GuzzleHttp\Cookie\CookieJar;
use Saloon\Http\Connector;

class DocuWareWithCookieConnector extends Connector
{
    public CookieJar $cookieJar;

    public function __construct(string $cookie)
    {
        $this->cookieJar = CookieJar::fromArray(
            [Auth::COOKIE_NAME => $cookie],
            parse_url(config('docuware.credentials.url'), PHP_URL_HOST)
        );
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
            'cookies' => $this->cookieJar,
        ];
    }

    public function getCoookieJar(): CookieJar
    {
        return $this->cookieJar;
    }
}
