<?php

namespace CodebarAg\DocuWare\Connectors;

use CodebarAg\DocuWare\Exceptions\UnableToFindUrlCredential;
use CodebarAg\DocuWare\Support\Auth;
use GuzzleHttp\Cookie\CookieJar;
use Saloon\Http\Connector;

class DocuWareStaticCookieConnector extends Connector
{
    public function __construct(protected ?string $cookie = null)
    {
        $this->cookie = self::getValidatedCookie($cookie);
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
            'cookies' => self::getCoookieJar(),
        ];
    }

    protected function getValidatedCookie(string $cookie = null): string
    {
        throw_if(
            empty($cookie ?? config('docuware.credentials.cookie')),
            UnableToFindUrlCredential::create(),
        );

        return $cookie ?? config('docuware.credentials.cookie');
    }

    public function getCoookieJar(): CookieJar
    {
        return CookieJar::fromArray(
            [Auth::COOKIE_NAME => $this->cookie],
            config('docuware.credentials.url'),
        );
    }
}
