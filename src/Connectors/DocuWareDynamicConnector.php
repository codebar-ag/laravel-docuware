<?php

namespace CodebarAg\DocuWare\Connectors;

use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\Support\Auth;
use GuzzleHttp\Cookie\CookieJar;
use Saloon\Http\Connector;

class DocuWareDynamicConnector extends Connector
{
    public Config $configuration;

    public CookieJar $cookieJar;

    public function __construct(Config $config)
    {
        $this->configuration = $config;

        $this->cookieJar = CookieJar::fromArray(
            [Auth::COOKIE_NAME => $this->configuration->cookie],
            parse_url($config->url, PHP_URL_HOST)
        );
    }

    /**
     * @throws \Exception
     */
    public function resolveBaseUrl(): string
    {
        return $this->configuration->url.'/DocuWare/Platform';
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
            'timeout' => $this->configuration->requestTimeoutInSeconds,
            'cookies' => $this->cookieJar,
        ];
    }

    public function getCoookieJar(): CookieJar
    {
        return $this->cookieJar;
    }

    public function getConfiguration(): Config
    {
        return $this->configuration;
    }
}
