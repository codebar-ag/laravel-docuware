<?php

use CodebarAg\DocuWare\Client\DocuWareClient;
use CodebarAg\DocuWare\Config\CredentialsConfig;
use CodebarAg\DocuWare\Config\TokenConfig;
use CodebarAg\DocuWare\DocuWareManager;

beforeEach(function () {
    config()->set('laravel-docuware.default', 'default');
    config()->set('laravel-docuware.configurations.cache.driver', 'array');
    config()->set('laravel-docuware.configurations.cache.lifetime_in_seconds', 60);
    config()->set('laravel-docuware.configurations.request.timeout_in_seconds', 60);
    config()->set('laravel-docuware.configurations.client_id', 'docuware.platform.net.client');
    config()->set('laravel-docuware.configurations.scope', 'docuware.platform');
});

it('resolves the manager from the container as a singleton', function () {
    expect(app(DocuWareManager::class))->toBe(app('docuware.manager'));
});

it('resolves a named instance and caches the client', function () {
    config()->set('laravel-docuware.instances.acme', [
        'grant' => 'credentials',
        'url' => 'https://acme.docuware.cloud',
        'username' => 'alice',
        'password' => 'secret',
    ]);

    $manager = app(DocuWareManager::class);

    $client = $manager->instance('acme');

    expect($client)->toBeInstanceOf(DocuWareClient::class)
        ->and($client->name())->toBe('acme')
        ->and($client->config)->toBeInstanceOf(CredentialsConfig::class)
        ->and($manager->instance('acme'))->toBe($client); // cached
});

it('falls back to legacy flat config for the default instance', function () {
    config()->set('laravel-docuware.instances', []);
    config()->set('laravel-docuware.credentials.url', 'https://legacy.docuware.cloud');
    config()->set('laravel-docuware.credentials.username', 'legacy-user');
    config()->set('laravel-docuware.credentials.password', 'legacy-pass');

    $config = app(DocuWareManager::class)->resolveConfig('default');

    expect($config)->toBeInstanceOf(CredentialsConfig::class)
        ->and($config->url)->toBe('https://legacy.docuware.cloud')
        ->and($config->username)->toBe('legacy-user');
});

it('supports a token-grant instance', function () {
    config()->set('laravel-docuware.instances.tok', [
        'grant' => 'token',
        'url' => 'https://tok.docuware.cloud',
        'token' => 'dwtoken-xyz',
    ]);

    expect(app(DocuWareManager::class)->resolveConfig('tok'))
        ->toBeInstanceOf(TokenConfig::class);
});

it('forgets cached clients', function () {
    config()->set('laravel-docuware.instances.acme', [
        'grant' => 'credentials',
        'url' => 'https://acme.docuware.cloud',
        'username' => 'alice',
        'password' => 'secret',
    ]);

    $manager = app(DocuWareManager::class);
    $first = $manager->instance('acme');
    $manager->forget('acme');

    expect($manager->instance('acme'))->not->toBe($first);
});
