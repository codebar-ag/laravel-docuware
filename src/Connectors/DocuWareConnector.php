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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Psr\SimpleCache\InvalidArgumentException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;

class DocuWareConnector extends Connector
{
    public function __construct(
        public ConfigWithCredentials|ConfigWithCredentialsTrustedUser $configuration
    ) {
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

        // get instance name of $this->configuration as string
        $instanceName = get_class($this->configuration);

        $cacheKey = 'docuware.oauth.'.$instanceName.'.'.$this->configuration->url.'.'.($this->configuration->username ?? '');

        $token = null;

        if ($cache->has(key: $cacheKey)) {
            $token = Crypt::decrypt($cache->get(key: $cacheKey));

            DocuWareOAuthLog::dispatch($this->configuration->url, $this->configuration->username, 'Token retrieved from cache');
        } else {
            if ($this->configuration instanceof ConfigWithCredentials) {
                $token = $this->getNewOAuthTokenWithCredentials();

                DocuWareOAuthLog::dispatch($this->configuration->url, $this->configuration->username, 'Token retrieved from API');
            }

            if ($this->configuration instanceof ConfigWithCredentialsTrustedUser) {
                $token = $this->getNewOAuthTokenWithCredentialsTrustedUser();

                DocuWareOAuthLog::dispatch($this->configuration->url, $this->configuration->username, 'Token retrieved from API');
            }

            $cache->put(key: $cacheKey, value: Crypt::encrypt($token), ttl: $token->expiresIn - 60);
        }

        return $token->accessToken;
    }

    protected function getAuthenticationTokenEndpoint(): IdentityServiceConfiguration
    {
        $responsibleIdentityServiceResponse = (new GetResponsibleIdentityService())->send();

        $identityServiceConfigurationResponse = (new GetIdentityServiceConfiguration(
            identityServiceUrl: $responsibleIdentityServiceResponse->dto()->identityServiceUrl
        ))->send();

        return $identityServiceConfigurationResponse->dto();
    }

    protected function getNewOAuthTokenWithCredentials(): RequestTokenDto
    {
        $requestTokenResponse = (new RequestTokenWithCredentials(
            tokenEndpoint: $this->getAuthenticationTokenEndpoint()->tokenEndpoint,
            clientId: $this->configuration->clientId,
            scope: $this->configuration->scope,
            username: $this->configuration->username,
            password: $this->configuration->password,
        ))->send();

        return $requestTokenResponse->dto();
    }

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

        return $requestTokenResponse->dto();
    }
}
