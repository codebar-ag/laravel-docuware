<?php

namespace CodebarAg\DocuWare\DTO\Config;

final class ConfigWithCredentialsTrustedUser
{
    public string $identifier;

    public string $username;

    public string $password;

    public string $impersonatedUsername;

    public string $url;

    public string $passphrase;

    public string $cacheDriver;

    public int $cacheLifetimeInSeconds;

    public int $requestTimeoutInSeconds;

    public string $clientId;

    public string $scope;

    public function __construct(
        string $username,
        string $password,
        string $impersonatedUsername,
        ?string $url = null,
        ?string $passphrase = null,
        ?string $cacheDriver = null,
        ?int $cacheLifetimeInSeconds = null,
        ?int $requestTimeoutInSeconds = null,
        ?string $clientId = null,
        ?string $scope = null,
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->impersonatedUsername = $impersonatedUsername;

        $this->url = filled($url) ? $url : config('laravel-docuware.credentials.url');

        $this->passphrase = filled($passphrase) ? $passphrase : config('laravel-docuware.passphrase');

        $this->cacheDriver = filled($cacheDriver) ? $cacheDriver : config('laravel-docuware.configurations.cache.driver');

        $this->cacheLifetimeInSeconds = filled($cacheLifetimeInSeconds) ? $cacheLifetimeInSeconds : config('laravel-docuware.configurations.cache.lifetime_in_seconds');

        $this->requestTimeoutInSeconds = filled($requestTimeoutInSeconds) ? $requestTimeoutInSeconds : config('laravel-docuware.configurations.request.timeout_in_seconds');

        $this->clientId = filled($clientId) ? $clientId : config('laravel-docuware.configurations.client_id');

        $this->scope = filled($scope) ? $scope : config('laravel-docuware.configurations.scope');

        $this->identifier = hash('sha256', $this->url.$this->username.$this->password);
    }
}
