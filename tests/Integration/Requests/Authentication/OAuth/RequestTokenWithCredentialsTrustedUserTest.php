<?php

use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken as RequestTokenDto;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetIdentityServiceConfiguration;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithCredentialsTrustedUser;

it('requests a token with the trusted-user grant', function () {
    $responsible = (new GetResponsibleIdentityService)->send()->dto();
    $openid = (new GetIdentityServiceConfiguration(
        identityServiceUrl: $responsible->identityServiceUrl
    ))->send()->dto();

    $token = (new RequestTokenWithCredentialsTrustedUser(
        tokenEndpoint: $openid->tokenEndpoint,
        username: config('laravel-docuware.credentials.username'),
        password: config('laravel-docuware.credentials.password'),
        impersonateName: config('laravel-docuware.credentials.username'),
    ))->send()->dto();

    expect($token)->toBeInstanceOf(RequestTokenDto::class);
})->group('authentication')->skip('Trusted-user grant is on-premises only; skip on cloud tenants.');
