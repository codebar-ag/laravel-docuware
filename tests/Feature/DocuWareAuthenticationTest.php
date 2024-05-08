<?php

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DTO\Authentication\OAuth\IdentityServiceConfiguration;
use CodebarAg\DocuWare\DTO\Authentication\OAuth\ResponsibleIdentityService;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;
use CodebarAg\DocuWare\Events\DocuWareOAuthLog;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetIdentityServiceConfiguration;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;
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

it('can authenticate with DocuWare Credentials', function () {
    Event::fake();

    $connector = new DocuWareConnector(new ConfigWithCredentials(
        username: config('laravel-docuware.credentials.username'),
        password: config('laravel-docuware.credentials.password'),
    ));

    $connector->send(new GetOrganization());

    Event::assertDispatched(DocuWareOAuthLog::class);

})->group('authentication');
