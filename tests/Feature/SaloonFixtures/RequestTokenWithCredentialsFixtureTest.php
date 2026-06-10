<?php

use CodebarAg\DocuWare\DTO\Authentication\OAuth\RequestToken as RequestTokenDto;
use CodebarAg\DocuWare\Requests\Authentication\OAuth\RequestTokenWithCredentials;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('maps RequestTokenWithCredentials through a mocked token response', function () {
    $mockClient = new MockClient([
        RequestTokenWithCredentials::class => MockResponse::make([
            'access_token' => 'fixture-access-token',
            'token_type' => 'Bearer',
            'scope' => 'docuware.platform',
            'expires_in' => 3600,
        ], 200),
    ]);

    $token = (new RequestTokenWithCredentials(tokenEndpoint: 'https://oauth.fixture.test/token'))
        ->withMockClient($mockClient)
        ->send()
        ->dto();

    expect($token)->toBeInstanceOf(RequestTokenDto::class)
        ->and($token->accessToken)->toBe('fixture-access-token');
});
