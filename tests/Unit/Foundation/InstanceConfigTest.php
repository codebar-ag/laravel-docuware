<?php

use CodebarAg\DocuWare\Config\CredentialsConfig;
use CodebarAg\DocuWare\Config\InstanceConfig;
use CodebarAg\DocuWare\Config\TokenConfig;
use CodebarAg\DocuWare\Config\TrustedUserConfig;
use CodebarAg\DocuWare\Enums\Grant;

it('builds a credentials config from attributes', function () {
    $config = InstanceConfig::make('default', [
        'grant' => 'credentials',
        'url' => 'https://example.docuware.cloud',
        'username' => 'alice',
        'password' => 'secret',
        'cacheDriver' => 'array',
        'cacheLifetimeInSeconds' => 30,
        'requestTimeoutInSeconds' => 20,
        'clientId' => 'cid',
        'scope' => 'docuware.platform',
    ]);

    expect($config)->toBeInstanceOf(CredentialsConfig::class)
        ->and($config->grant())->toBe(Grant::Credentials)
        ->and($config->username)->toBe('alice')
        ->and($config->cacheDriver)->toBe('array')
        ->and($config->identifier())->toBe(hash('sha256', 'default|https://example.docuware.cloud|alice|secret'));
});

it('builds a trusted-user config', function () {
    $config = InstanceConfig::make('tenant', [
        'grant' => 'trusted_user',
        'url' => 'https://example.docuware.cloud',
        'username' => 'trusted',
        'password' => 'secret',
        'impersonate' => 'bob',
    ]);

    expect($config)->toBeInstanceOf(TrustedUserConfig::class)
        ->and($config->impersonatedUsername)->toBe('bob')
        ->and($config->grant())->toBe(Grant::TrustedUser);
});

it('builds a token config', function () {
    $config = InstanceConfig::make('tok', [
        'grant' => 'token',
        'url' => 'https://example.docuware.cloud',
        'token' => 'dwtoken-abc',
    ]);

    expect($config)->toBeInstanceOf(TokenConfig::class)
        ->and($config->token)->toBe('dwtoken-abc')
        ->and($config->username)->toBe('');
});

it('throws a non-secret error when a required key is missing', function () {
    InstanceConfig::make('default', [
        'grant' => 'credentials',
        'url' => 'https://example.docuware.cloud',
        'username' => 'alice',
        // password missing
    ]);
})->throws(InvalidArgumentException::class, 'missing required config key [password]');

it('namespaces the identifier by instance name', function () {
    $base = [
        'grant' => 'credentials',
        'url' => 'https://example.docuware.cloud',
        'username' => 'alice',
        'password' => 'secret',
    ];

    $a = InstanceConfig::make('tenant-a', $base);
    $b = InstanceConfig::make('tenant-b', $base);

    expect($a->identifier())->not->toBe($b->identifier());
});
