<?php

namespace CodebarAg\DocuWare\Connectors;

use CodebarAg\DocuWare\DTO\Authentication\OAuth\IdentityServiceConfiguration;
use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken as RequestTokenDto;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentialsTrustedUser;
use CodebarAg\DocuWare\Events\DocuWareOAuthLog;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetIdentityServiceConfiguration;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithCredentials;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithCredentialsTrustedUser;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Psr\SimpleCache\InvalidArgumentException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;

class DocuWareConnector extends Connector
{
    public function __construct(
        public ConfigWithCredentials|ConfigWithCredentialsTrustedUser $configuration
    ) {}

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

        $cacheKey = 'docuware.oauth.'.$this->configuration->identifier;

        // Check if the token exists in cache and return it if found
        if ($cache->has($cacheKey)) {
            $token = Crypt::decrypt($cache->get($cacheKey));
            DocuWareOAuthLog::dispatch($this->configuration->url, $this->configuration->username, 'Token retrieved from cache');

            return $token->accessToken;
        }

        // Handle token retrieval for ConfigWithCredentials
        if ($this->configuration instanceof ConfigWithCredentials) {
            $token = $this->getNewOAuthTokenWithCredentials();
            DocuWareOAuthLog::dispatch($this->configuration->url, $this->configuration->username, 'Token retrieved from API');
            $cache->put($cacheKey, Crypt::encrypt($token), $token->expiresIn - 60);

            return $token->accessToken;
        }

        // Handle token retrieval for ConfigWithCredentialsTrustedUser
        if ($this->configuration instanceof ConfigWithCredentialsTrustedUser) {
            $token = $this->getNewOAuthTokenWithCredentialsTrustedUser();
            DocuWareOAuthLog::dispatch($this->configuration->url, $this->configuration->username, 'Token retrieved from API');
            $cache->put($cacheKey, Crypt::encrypt($token), $token->expiresIn - 60);

            return $token->accessToken;
        }

        // If configuration type is unsupported, throw an exception
        throw new \Exception('Unsupported configuration type');
    }

    protected function getAuthenticationTokenEndpoint(): IdentityServiceConfiguration
    {
        $responsibleIdentityServiceResponse = (new GetResponsibleIdentityService($this->configuration->url))->send();

        $identityServiceConfigurationResponse = (new GetIdentityServiceConfiguration(
            identityServiceUrl: $responsibleIdentityServiceResponse->dto()->identityServiceUrl
        ))->send();

        return $identityServiceConfigurationResponse->dto();
    }

    /**
     * @throws \Throwable
     * @throws \JsonException
     */
    protected function getNewOAuthTokenWithCredentials(): RequestTokenDto
    {
        $requestTokenResponse = (new RequestTokenWithCredentials(
            tokenEndpoint: $this->getAuthenticationTokenEndpoint()->tokenEndpoint,
            clientId: $this->configuration->clientId,
            scope: $this->configuration->scope,
            username: $this->configuration->username,
            password: $this->configuration->password,
        ))->send();

        throw_if(
            $requestTokenResponse->failed(),
            trim(preg_replace('/\s\s+/', ' ', Arr::get(
                array: $requestTokenResponse->json(),
                key: 'error_description',
                default: $requestTokenResponse->body()
            )))
        );

        throw_if($requestTokenResponse->dto() == null, 'Token response is null');

        return $requestTokenResponse->dto();
    }

    /**
     * @throws \Throwable
     * @throws \JsonException
     */
    protected function getNewOAuthTokenWithCredentialsTrustedUser(): RequestTokenDto
    {
        $requestTokenResponse = (new RequestTokenWithCredentialsTrustedUser(
            tokenEndpoint: $this->getAuthenticationTokenEndpoint()->tokenEndpoint,
            clientId: $this->configuration->clientId,
            scope: $this->configuration->scope,
            username: $this->configuration->username,
            password: $this->configuration->password,
            impersonateName: $this->configuration->impersonatedUsername,
        ))->send();

        throw_if(
            $requestTokenResponse->failed(),
            trim(preg_replace('/\s\s+/', ' ', Arr::get(
                array: $requestTokenResponse->json(),
                key: 'error_description',
                default: $requestTokenResponse->body()
            )))
        );

        throw_if($requestTokenResponse->dto() == null, 'Token response is null');

        return $requestTokenResponse->dto();
    }
}
