<?php

use CodebarAg\DocuWare\Security\Redactor;

it('redacts secret keys in nested arrays', function () {
    $redacted = (new Redactor)->redact([
        'Authorization' => 'Bearer abc.def.ghi',
        'nested' => [
            'password' => 'hunter2',
            'username' => 'alice',
        ],
        'list' => [['token' => 't0ken'], ['safe' => 'ok']],
    ]);

    expect($redacted['Authorization'])->toBe(Redactor::PLACEHOLDER)
        ->and($redacted['nested']['password'])->toBe(Redactor::PLACEHOLDER)
        ->and($redacted['nested']['username'])->toBe('alice')
        ->and($redacted['list'][0]['token'])->toBe(Redactor::PLACEHOLDER)
        ->and($redacted['list'][1]['safe'])->toBe('ok');
});

it('redacts bearer tokens and secret fragments in strings', function () {
    $redactor = new Redactor;

    expect($redactor->string('Authorization: Bearer abc.def-ghi123'))
        ->toContain(Redactor::PLACEHOLDER)
        ->not->toContain('abc.def-ghi123');

    expect($redactor->string('grant_type=password&password=hunter2&scope=x'))
        ->toContain('password='.Redactor::PLACEHOLDER)
        ->not->toContain('hunter2');

    expect($redactor->string('{"access_token":"s3cret","expires_in":3600}'))
        ->toContain(Redactor::PLACEHOLDER)
        ->not->toContain('s3cret');
});

it('leaves non-secret scalars untouched', function () {
    expect((new Redactor)->redact(42))->toBe(42)
        ->and((new Redactor)->redact('plain text'))->toBe('plain text');
});
