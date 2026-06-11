<?php

use CodebarAg\DocuWare\Data\Authentication\ResponsibleIdentityServiceData;

it('parses a well-formed discovery payload', function () {
    $data = ResponsibleIdentityServiceData::fromDocuWare([
        'IdentityServiceUrl' => 'https://login-emea.docuware.cloud/abc',
        'RefreshTokenSupported' => true,
    ]);

    expect($data->identityServiceUrl)->toBe('https://login-emea.docuware.cloud/abc')
        ->and($data->refreshTokenSupported)->toBeTrue();
});

it('does not throw when the discovery response is empty or missing the URL', function () {
    $data = ResponsibleIdentityServiceData::fromDocuWare([]);

    expect($data->identityServiceUrl)->toBeNull()
        ->and($data->refreshTokenSupported)->toBeFalse();
});

it('treats a blank URL as null', function () {
    $data = ResponsibleIdentityServiceData::fromDocuWare([
        'IdentityServiceUrl' => '',
        'RefreshTokenSupported' => false,
    ]);

    expect($data->identityServiceUrl)->toBeNull();
});

it('coerces a stringy boolean from the XML discovery path', function () {
    // simplexml -> json yields string scalars (e.g. "true") rather than real booleans.
    $data = ResponsibleIdentityServiceData::fromDocuWare([
        'IdentityServiceUrl' => 'https://login.example.test/abc',
        'RefreshTokenSupported' => 'true',
    ]);

    expect($data->refreshTokenSupported)->toBeTrue();
});
