<?php

use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken as RequestTokenDto;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetIdentityServiceConfiguration;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithCredentials;

it('requests a token with username and password', function () {
    $responsible = (new GetResponsibleIdentityService)->send()->dto();
    $openid = (new GetIdentityServiceConfiguration(
        identityServiceUrl: $responsible->identityServiceUrl
    ))->send()->dto();

    $token = (new RequestTokenWithCredentials(
        tokenEndpoint: $openid->tokenEndpoint,
    ))->send()->dto();

    expect($token)->toBeInstanceOf(RequestTokenDto::class)
        ->and($token->accessToken)->not->toBeEmpty();
})->group('authentication');
