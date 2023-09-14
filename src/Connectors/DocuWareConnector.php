<?php

namespace CodebarAg\DocuWare\Connectors;

use CodebarAg\DocuWare\Events\DocuWareAuthenticateLog;
use CodebarAg\DocuWare\Support\Auth;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use CodebarAg\DocuWare\Support\EnsureValidCredentials;
use GuzzleHttp\Cookie\CookieJar;
use Saloon\Http\Connector;

class DocuWareConnector extends Connector
{
    public CookieJar $cookieJar;

    public function __construct(string $cookie = null)
    {
        if ($cookie) {
            event(new DocuWareAuthenticateLog('Authenticating with cookie'));

            $this->cookieJar = CookieJar::fromArray(
                [Auth::COOKIE_NAME => $cookie],
                parse_url(config('docuware.credentials.url'), PHP_URL_HOST)
            );
        } else {
            EnsureValidCredentials::check();
            EnsureValidCookie::check();

            $this->cookieJar = Auth::cookieJar() ?? throw new \Exception('No cookie jar found');
        }
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
