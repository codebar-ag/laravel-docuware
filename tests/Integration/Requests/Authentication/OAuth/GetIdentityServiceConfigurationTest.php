<?php

use CodebarAg\DocuWare\DTO\Authentication\OAuth\IdentityServiceConfiguration;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetIdentityServiceConfiguration;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;

it('returns OpenID configuration for the identity service', function () {
    $responsible = (new GetResponsibleIdentityService)->send()->dto();

    $response = (new GetIdentityServiceConfiguration(
        identityServiceUrl: $responsible->identityServiceUrl
    ))->send();

    expect($response->dto())->toBeInstanceOf(IdentityServiceConfiguration::class)
        ->and($response->dto()->tokenEndpoint)->not->toBeEmpty();
})->group('authentication');
