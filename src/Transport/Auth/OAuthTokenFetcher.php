<?php

namespace CodebarAg\DocuWare\Transport\Auth;

use CodebarAg\DocuWare\Config\CredentialsConfig;
use CodebarAg\DocuWare\Config\InstanceConfig;
use CodebarAg\DocuWare\Config\TokenConfig;
use CodebarAg\DocuWare\Config\TrustedUserConfig;
use CodebarAg\DocuWare\Data\Authentication\IdentityServiceConfigurationData;
use CodebarAg\DocuWare\Data\Authentication\RequestTokenData;
use CodebarAg\DocuWare\Data\Authentication\ResponsibleIdentityServiceData;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetIdentityServiceConfiguration;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithCredentials;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithCredentialsTrustedUser;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithDocuWareToken;
use CodebarAg\DocuWare\Support\ResponseBody;
use LogicException;
use RuntimeException;
use Saloon\Http\Response;

/**
 * Performs the OAuth token exchange for an instance: discovers the token endpoint, then
 * requests an access token using the instance's grant. Returns the parsed token; caching and
 * concurrency control are the {@see TokenRepository}'s job.
 *
 * @internal
 */
final class OAuthTokenFetcher
{
    public function fetch(InstanceConfig $config): RequestTokenData
    {
        $tokenEndpoint = $this->discoverTokenEndpoint($config);

        $request = match (true) {
            $config instanceof CredentialsConfig => new RequestTokenWithCredentials(
                tokenEndpoint: $tokenEndpoint,
                clientId: $config->clientId,
                scope: $config->scope,
                username: $config->username,
                password: $config->password,
            ),
            $config instanceof TrustedUserConfig => new RequestTokenWithCredentialsTrustedUser(
                tokenEndpoint: $tokenEndpoint,
                clientId: $config->clientId,
                scope: $config->scope,
                username: $config->username,
                password: $config->password,
                impersonateName: $config->impersonatedUsername,
            ),
            $config instanceof TokenConfig => new RequestTokenWithDocuWareToken(
                tokenEndpoint: $tokenEndpoint,
                token: $config->token,
                clientId: $config->clientId,
                scope: $config->scope,
            ),
            default => throw new LogicException('Unsupported instance config: '.$config::class),
        };

        $response = $request->send();

        if ($response->failed()) {
            throw new RuntimeException($this->failureMessage($response));
        }

        return RequestTokenData::fromDocuWare($response->json());
    }

    private function discoverTokenEndpoint(InstanceConfig $config): string
    {
        $identityResponse = (new GetResponsibleIdentityService($config->url))->send();
        $identity = ResponsibleIdentityServiceData::fromDocuWare(ResponseBody::toArray($identityResponse));

        $configResponse = (new GetIdentityServiceConfiguration(
            identityServiceUrl: $identity->identityServiceUrl,
        ))->send();
        $configuration = IdentityServiceConfigurationData::fromDocuWare(ResponseBody::toArray($configResponse));

        $endpoint = $configuration->tokenEndpoint;

        if (! is_string($endpoint) || $endpoint === '') {
            throw new RuntimeException("Could not discover the OAuth token endpoint for instance [{$config->name}].");
        }

        return $endpoint;
    }

    private function failureMessage(Response $response): string
    {
        $body = (string) $response->body();

        /** @var mixed $decoded */
        $decoded = json_decode($body, true);

        if (is_array($decoded)) {
            $message = $decoded['error_description'] ?? $decoded['error'] ?? null;
            if (is_string($message) && $message !== '') {
                return trim((string) preg_replace('/\s\s+/', ' ', $message));
            }
        }

        return 'OAuth token request failed with HTTP '.$response->status().'.';
    }
}
