<?php

namespace CodebarAg\DocuWare\DTO\Config;

/**
 * Authenticate using the DocuWare token grant (`grant_type=dwtoken`).
 *
 * The `$token` is a DocuWare login token (e.g. obtained via the "Get Login Token" request or
 * handed to you by DocuWare). It is exchanged for an OAuth access token. This pairs with the
 * Postman "3.b Request Token w/ a DocuWare Token" flow.
 */
final class ConfigWithDocuWareToken
{
    public string $identifier;

    public string $token;

    public string $username;

    public string $url;

    public ?string $passphrase;

    public string $cacheDriver;

    public int $cacheLifetimeInSeconds;

    public int $requestTimeoutInSeconds;

    public string $clientId;

    public string $scope;

    public function __construct(
        string $token,
        ?string $url = null,
        string $username = '',
        ?string $passphrase = null,
        ?string $cacheDriver = null,
        ?int $cacheLifetimeInSeconds = null,
        ?int $requestTimeoutInSeconds = null,
        ?string $clientId = null,
        ?string $scope = null,
    ) {
        $this->token = $token;
        $this->username = $username;

        $this->url = filled($url) ? $url : config('laravel-docuware.credentials.url');

        $this->passphrase = filled($passphrase) ? $passphrase : config('laravel-docuware.passphrase');

        $this->cacheDriver = filled($cacheDriver) ? $cacheDriver : config('laravel-docuware.configurations.cache.driver');

        $this->cacheLifetimeInSeconds = filled($cacheLifetimeInSeconds) ? $cacheLifetimeInSeconds : config('laravel-docuware.configurations.cache.lifetime_in_seconds');

        $this->requestTimeoutInSeconds = filled($requestTimeoutInSeconds) ? $requestTimeoutInSeconds : config('laravel-docuware.configurations.request.timeout_in_seconds');

        $this->clientId = filled($clientId) ? $clientId : config('laravel-docuware.configurations.client_id');

        $this->scope = filled($scope) ? $scope : config('laravel-docuware.configurations.scope');

        $this->identifier = hash('sha256', $this->url.$this->token);
    }
}
