<?php

use CodebarAg\DocuWare\DTO\Authentication\OAuth\IdentityServiceConfiguration;
use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken;
use CodebarAg\DocuWare\DTO\Authentication\OAuth\ResponsibleIdentityService;
use CodebarAg\DocuWare\Facades\DocuWare;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetIdentityServiceConfiguration;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;

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

    $requestTokenResponse = (new \CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestToken(
        tokenEndpoint: $identityServiceConfigurationResponse->dto()->tokenEndpoint
    ))->send();

    expect($requestTokenResponse->dto())->toBeInstanceOf(RequestToken::class);
})->group('authentication');

it('can authenticate with DocuWare', function () {
    $res = DocuWare::getNewAuthenticationOAuthToken();

    ray($res);

    expect($res)->toBeInstanceOf(RequestToken::class);
})->group('authentication');
