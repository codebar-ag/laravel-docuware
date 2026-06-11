<?php

use CodebarAg\DocuWare\DTO\Authentication\OAuth\ResponsibleIdentityService;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    config()->set('laravel-docuware.credentials.url', 'https://example.docuware.cloud');
});

it('parses an XML IdentityServiceInfo response', function () {
    $xml = '<?xml version="1.0" encoding="utf-8"?>'
        .'<IdentityServiceInfo xmlns:s="http://dev.docuware.com/schema/public/services" '
        .'xmlns="http://dev.docuware.com/schema/public/services/platform">'
        .'<IdentityServiceUrl>https://login-emea.docuware.cloud/cc60ed60</IdentityServiceUrl>'
        .'<RefreshTokenSupported>true</RefreshTokenSupported>'
        .'</IdentityServiceInfo>';

    $mockClient = new MockClient([
        GetResponsibleIdentityService::class => MockResponse::make($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]),
    ]);

    $dto = (new GetResponsibleIdentityService)
        ->withMockClient($mockClient)
        ->send()
        ->dto();

    expect($dto)->toBeInstanceOf(ResponsibleIdentityService::class)
        ->and($dto->identityServiceUrl)->toBe('https://login-emea.docuware.cloud/cc60ed60')
        ->and($dto->refreshTokenSupported)->toBeTrue();
})->group('unit');

it('parses a JSON IdentityServiceInfo response', function () {
    $mockClient = new MockClient([
        GetResponsibleIdentityService::class => MockResponse::make([
            'IdentityServiceUrl' => 'https://login-emea.docuware.cloud/cc60ed60',
            'RefreshTokenSupported' => true,
        ], 200),
    ]);

    $dto = (new GetResponsibleIdentityService)
        ->withMockClient($mockClient)
        ->send()
        ->dto();

    expect($dto)->toBeInstanceOf(ResponsibleIdentityService::class)
        ->and($dto->identityServiceUrl)->toBe('https://login-emea.docuware.cloud/cc60ed60')
        ->and($dto->refreshTokenSupported)->toBeTrue();
})->group('unit');
