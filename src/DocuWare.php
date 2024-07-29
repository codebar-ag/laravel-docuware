<?php

namespace CodebarAg\DocuWare;

use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken as RequestTokenDto;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetIdentityServiceConfiguration;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithCredentials;

class DocuWare
{
    public function getNewAuthenticationOAuthToken(
        ?string $username = '',
        ?string $password = '',
        ?string $grantType = 'password',
        ?string $clientId = 'docuware.platform.net.client'
    ): RequestTokenDto {
        $responsibleIdentityServiceResponse = (new GetResponsibleIdentityService)->send();

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

    public function searchRequestBuilder(): DocuWareSearchRequestBuilder
    {
        return new DocuWareSearchRequestBuilder;
    }

    public function url(): DocuWareUrl
    {
        return new DocuWareUrl;
    }
}
