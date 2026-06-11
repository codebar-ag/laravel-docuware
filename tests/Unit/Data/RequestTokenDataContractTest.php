<?php

use CodebarAg\DocuWare\Data\Authentication\RequestTokenData;
use CodebarAg\DocuWare\Exceptions\MalformedResponseException;
use Illuminate\Support\Carbon;

it('builds a usable token from a well-formed OAuth response', function () {
    $token = RequestTokenData::fromDocuWare([
        'access_token' => 'abc',
        'token_type' => 'Bearer',
        'scope' => 'docuware.platform',
        'expires_in' => 3600,
    ]);

    expect($token->accessToken)->toBe('abc')
        ->and($token->expiresIn)->toBe(3600)
        ->and($token->expiresAt->isFuture())->toBeTrue();
});

it('refuses a token response with no expires_in instead of minting an already-expired token', function () {
    // Regression: previously addSeconds(null) produced expiresAt = now → a silently dead token.
    expect(fn () => RequestTokenData::fromDocuWare([
        'access_token' => 'abc',
        'token_type' => 'Bearer',
    ]))->toThrow(MalformedResponseException::class, 'expires_in');
});

it('refuses a token response with no access_token', function () {
    expect(fn () => RequestTokenData::fromDocuWare(['expires_in' => 3600]))
        ->toThrow(MalformedResponseException::class, 'access_token');
});

it('coerces a string expires_in to an int', function () {
    $token = RequestTokenData::fromDocuWare(['access_token' => 'abc', 'expires_in' => '120']);

    expect($token->expiresIn)->toBe(120)
        ->and($token->expiresAt->greaterThan(Carbon::now()->addSeconds(60)))->toBeTrue();
});
