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
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Response;

class DocuWareConnector extends Connector
{
    public function __construct(
        public ConfigWithCredentials|ConfigWithCredentialsTrustedUser $configuration
    ) {}

    public function resolveBaseUrl(): string
    {
        $base = rtrim($this->configuration->url, '/');
        $platform = trim(config('laravel-docuware.platform_path', 'DocuWare/Platform'), '/');

        return $base.'/'.$platform;
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

    protected function oauthTokenCacheTtlSeconds(RequestTokenDto $token): int
    {
        return max(1, $token->expiresIn - 60);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function getOrCreateNewOAuthToken(): string
    {
        $cache = Cache::store($this->configuration->cacheDriver);

        $cacheKey = 'docuware.oauth.'.$this->configuration->identifier;

        if ($cache->has($cacheKey)) {
            $token = Crypt::decrypt($cache->get($cacheKey));
            DocuWareOAuthLog::dispatch($this->configuration->url, $this->configuration->username, 'Token retrieved from cache');

            return $token->accessToken;
        }

        $token = $this->configuration instanceof ConfigWithCredentials
            ? $this->getNewOAuthTokenWithCredentials()
            : $this->getNewOAuthTokenWithCredentialsTrustedUser();

        DocuWareOAuthLog::dispatch($this->configuration->url, $this->configuration->username, 'Token retrieved from API');
        $cache->put($cacheKey, Crypt::encrypt($token), $this->oauthTokenCacheTtlSeconds($token));

        return $token->accessToken;
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

        return $this->ensureRequestTokenSuccess($requestTokenResponse);
    }

    /**
     * @throws \Throwable
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

        return $this->ensureRequestTokenSuccess($requestTokenResponse);
    }

    /**
     * @throws \Throwable
     */
    protected function ensureRequestTokenSuccess(Response $requestTokenResponse): RequestTokenDto
    {
        if ($requestTokenResponse->failed()) {
            throw new \RuntimeException($this->oauthTokenFailureMessage($requestTokenResponse));
        }

        throw_if($requestTokenResponse->dto() == null, 'Token response is null');

        return $requestTokenResponse->dto();
    }

    /**
     * Build a safe error message when the token endpoint returns a failure.
     * Uses non-throwing {@see json_decode()} so HTML or other non-JSON bodies do not throw.
     */
    protected function oauthTokenFailureMessage(Response $response): string
    {
        $body = (string) $response->body();

        /** @var mixed $decoded */
        $decoded = json_decode($body, true);
        if (! is_array($decoded)) {
            return $this->oauthFailureBodySummary($body, $response);
        }

        $message = Arr::get($decoded, 'error_description')
            ?? Arr::get($decoded, 'error');
        if (! is_string($message) || $message === '') {
            return $this->oauthFailureBodySummary($body, $response);
        }

        return trim(preg_replace('/\s\s+/', ' ', $message));
    }

    protected function oauthFailureBodySummary(string $body, Response $response): string
    {
        $trimmed = trim(preg_replace('/\s\s+/', ' ', $body));
        if ($trimmed !== '') {
            return Str::limit($trimmed, 500);
        }

        return 'OAuth token request failed with HTTP '.$response->status().'.';
    }
}
