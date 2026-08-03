<?php

use CodebarAg\DocuWare\DTO\Config\ConfigWithDocuWareToken;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithDocuWareToken;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('sends the dwtoken grant body', function () {
    $request = new RequestTokenWithDocuWareToken(
        tokenEndpoint: 'https://oauth.fixture.test/token',
        token: 'login-token-123',
    );

    expect($request->body()->all())->toBe([
        'grant_type' => 'dwtoken',
        'scope' => 'docuware.platform',
        'client_id' => 'docuware.platform.net.client',
        'token' => 'login-token-123',
    ]);
})->group('unit');

it('exchanges a DocuWare login token for an access token', function () {
    $mockClient = new MockClient([
        RequestTokenWithDocuWareToken::class => MockResponse::make([
            'access_token' => 'dwtoken-access-token',
            'token_type' => 'Bearer',
            'scope' => 'docuware.platform',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = (new RequestTokenWithDocuWareToken('https://oauth.fixture.test/token', 'login-token-123'))
        ->withMockClient($mockClient)
        ->send();

    expect($response->json('access_token'))->toBe('dwtoken-access-token');
})->group('unit');

it('derives a stable cache identifier from url and token', function () {
    config()->set('laravel-docuware.credentials.url', 'https://example.docuware.cloud');

    $config = new ConfigWithDocuWareToken(token: 'login-token-123');

    expect($config->token)->toBe('login-token-123')
        ->and($config->url)->toBe('https://example.docuware.cloud')
        ->and($config->identifier)->toBe(hash('sha256', 'https://example.docuware.cloud'.'login-token-123'));
})->group('unit');
