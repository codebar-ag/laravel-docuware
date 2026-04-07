<?php

use CodebarAg\DocuWare\DTO\Authentication\OAuth\ResponsibleIdentityService;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;

it('returns responsible identity service info', function () {
    $response = (new GetResponsibleIdentityService)->send();

    expect($response->dto())->toBeInstanceOf(ResponsibleIdentityService::class)
        ->and($response->dto()->identityServiceUrl)->not->toBeEmpty();
})->group('authentication');
