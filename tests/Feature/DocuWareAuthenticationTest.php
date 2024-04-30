<?php

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DTO\Authentication\OAuth\IdentityServiceConfiguration;
use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken;
use CodebarAg\DocuWare\DTO\Authentication\OAuth\ResponsibleIdentityService;
use CodebarAg\DocuWare\Events\DocuWareOAuthLog;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetIdentityServiceConfiguration;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;
use CodebarAg\DocuWare\Requests\General\Organization\GetLoginToken;
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;

it('can get oath responsible identity service', function () {
    $responsibleIdentityServiceResponse = (new GetResponsibleIdentityService())->send();

    expect($responsibleIdentityServiceResponse->dto())->toBeInstanceOf(ResponsibleIdentityService::class);
})->group('authentication');

it('can get oath identity service configuration', function () {
    $responsibleIdentityServiceResponse = (new GetResponsibleIdentityService())->send();

    $identityServiceConfigurationResponse = (new GetIdentityServiceConfiguration(
        identityServiceUrl: $responsibleIdentityServiceResponse->dto()->identityServiceUrl
    ))->send();

    expect($identityServiceConfigurationResponse->dto())->toBeInstanceOf(IdentityServiceConfiguration::class);
})->group('authentication');

it('can get oath request token', function () {
    $responsibleIdentityServiceResponse = (new GetResponsibleIdentityService())->send();

    $identityServiceConfigurationResponse = (new GetIdentityServiceConfiguration(
        identityServiceUrl: $responsibleIdentityServiceResponse->dto()->identityServiceUrl
    ))->send();

    $requestTokenResponse = (new \CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithCredentials(
        tokenEndpoint: $identityServiceConfigurationResponse->dto()->tokenEndpoint
    ))->send();

    expect($requestTokenResponse->dto())->toBeInstanceOf(RequestToken::class);
})->group('authentication');

it('can authenticate with DocuWare Credentials', function () {
    Event::fake();

    $connector = new DocuWareConnector();
    $connector->send(new GetOrganization());

    Event::assertDispatched(DocuWareOAuthLog::class);

})->group('authentication')->only();

it('can authenticate with DocuWare Token', function () {
    Event::fake();

    $connector = new DocuWareConnector();
    $token = $connector->send(new GetLoginToken())->dto();

    $connector = new DocuWareConnector(token: $token);

    $connector->send(new GetOrganization());

    Event::assertDispatched(DocuWareOAuthLog::class);

})->group('authentication')->only();
