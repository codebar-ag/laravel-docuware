<?php

namespace CodebarAg\DocuWare\Connectors;

use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken as RequestTokenDto;
use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\Events\DocuWareOAuthLog;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetIdentityServiceConfiguration;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithCredentials;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithToken;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;

class DocuWareConnector extends Connector
{
    public Config $configuration;

    public null|string $token = null;

    public function __construct(null|Config $configuration = null, null|string $token = null) {
        if (empty($configuration)) {
            $this->configuration = new Config(
                url: config('laravel-docuware.credentials.url'),
                username: config('laravel-docuware.credentials.username'),
                password: config('laravel-docuware.credentials.password'),
                passphrase: config('laravel-docuware.passphrase'),
                cacheDriver: config('laravel-docuware.configurations.cache.driver'),
                cacheLifetimeInSeconds: config('laravel-docuware.configurations.cache.lifetime_in_seconds'),
                requestTimeoutInSeconds: config('laravel-docuware.configurations.request.timeout_in_seconds'),
            );
        }else{
            $this->configuration = $configuration;
        }

        $this->token = $token;
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
            'timeout' => $this->configuration->requestTimeoutInSeconds,
        ];
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->getOrCreateNewOAuthToken());
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function getOrCreateNewOAuthToken()
    {
        $cache = Cache::store($this->configuration->cacheDriver);
        $cacheKey = 'docuware.oauth.token.'.$this->configuration->url.'.'.$this->configuration->username;

        if (filled($this->token)) {
            $token = $this->getNewAuthenticationOAuthTokenWithToken($this->token);

            $cache->put(key: $cacheKey, value: $token, ttl: $token->expiresIn);

            DocuWareOAuthLog::dispatch($this->configuration->url, $this->configuration->username, 'Token set from token');
        }

        if (
            $cache->has(key: $cacheKey)
        ){
            $token = $cache->get(key: $cacheKey);

            DocuWareOAuthLog::dispatch($this->configuration->url, $this->configuration->username, 'Token retrieved from cache');
        }else{
            $token = $this->getNewAuthenticationOAuthTokenWithCredentials($this->configuration->username, $this->configuration->password);

            $cache->put(key: $cacheKey, value: $token, ttl: $token->expiresIn);

            DocuWareOAuthLog::dispatch($this->configuration->url, $this->configuration->username, 'Token retrieved from API');
        }

        return $token->accessToken;
    }

    protected function getNewAuthenticationOAuthTokenWithCredentials(
        ?string $username = '',
        ?string $password = '',
        ?string $clientId = 'docuware.platform.net.client'
    ): RequestTokenDto {
        $responsibleIdentityServiceResponse = (new GetResponsibleIdentityService())->send();

        $identityServiceConfigurationResponse = (new GetIdentityServiceConfiguration(
            identityServiceUrl: $responsibleIdentityServiceResponse->dto()->identityServiceUrl
        ))->send();

        $requestTokenResponse = (new RequestTokenWithCredentials(
            tokenEndpoint: $identityServiceConfigurationResponse->dto()->tokenEndpoint,
            username: $username,
            password: $password,
            clientId: $clientId,
        ))->send();

        return $requestTokenResponse->dto();
    }

    protected function getNewAuthenticationOAuthTokenWithToken(
        string $token,
        ?string $clientId = 'docuware.platform.net.client'
    ): RequestTokenDto {
        $responsibleIdentityServiceResponse = (new GetResponsibleIdentityService())->send();

        $identityServiceConfigurationResponse = (new GetIdentityServiceConfiguration(
            identityServiceUrl: $responsibleIdentityServiceResponse->dto()->identityServiceUrl
        ))->send();

        $requestTokenResponse = (new RequestTokenWithToken(
            tokenEndpoint: $identityServiceConfigurationResponse->dto()->tokenEndpoint,
            token: $token,
            clientId: $clientId,
        ))->send();

        return $requestTokenResponse->dto();
    }
}
