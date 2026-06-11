<?php

use CodebarAg\DocuWare\Client\DocuWareClient;
use CodebarAg\DocuWare\Config\CredentialsConfig;
use CodebarAg\DocuWare\Config\InstanceConfig;
use CodebarAg\DocuWare\Config\TokenConfig;
use CodebarAg\DocuWare\Config\TrustedUserConfig;
use CodebarAg\DocuWare\DocuWareManager;
use CodebarAg\DocuWare\Facades\DocuWare;

it('builds a client from a runtime credentials DTO via connection()', function () {
    $config = CredentialsConfig::for(
        url: 'https://acme.docuware.cloud',
        username: 'alice',
        password: 'secret',
        name: 'acme',
    );

    $client = DocuWare::connection($config);

    expect($client)->toBeInstanceOf(DocuWareClient::class)
        ->and($client->name())->toBe('acme')
        ->and($client->config)->toBe($config);
});

it('accepts an InstanceConfig DTO directly in instance()', function () {
    $config = TokenConfig::for(
        url: 'https://tok.docuware.cloud',
        token: 'dwtoken-xyz',
        name: 'tok',
    );

    $client = DocuWare::instance($config);

    expect($client->config)->toBeInstanceOf(TokenConfig::class)
        ->and($client->name())->toBe('tok');
});

it('does not name-cache runtime connections, keeping tenants isolated', function () {
    $manager = app(DocuWareManager::class);

    $first = $manager->connection(
        CredentialsConfig::for('https://one.docuware.cloud', 'a', 'p', name: 'shared'),
    );
    $second = $manager->connection(
        CredentialsConfig::for('https://two.docuware.cloud', 'b', 'p', name: 'shared'),
    );

    expect($second)->not->toBe($first)
        ->and($first->config->identifier())->not->toBe($second->config->identifier());
});

it('fills sensible defaults on the for() factories', function () {
    $config = CredentialsConfig::for('https://acme.docuware.cloud', 'alice', 'secret');

    expect($config->name)->toBe('runtime')
        ->and($config->cacheDriver)->toBe(InstanceConfig::DEFAULT_CACHE_DRIVER)
        ->and($config->cacheLifetimeInSeconds)->toBe(InstanceConfig::DEFAULT_CACHE_LIFETIME_IN_SECONDS)
        ->and($config->requestTimeoutInSeconds)->toBe(InstanceConfig::DEFAULT_REQUEST_TIMEOUT_IN_SECONDS)
        ->and($config->clientId)->toBe(InstanceConfig::DEFAULT_CLIENT_ID)
        ->and($config->scope)->toBe(InstanceConfig::DEFAULT_SCOPE);
});

it('builds a trusted-user config at runtime', function () {
    $config = TrustedUserConfig::for(
        url: 'https://acme.docuware.cloud',
        username: 'trusted',
        password: 'secret',
        impersonate: 'bob',
        name: 'acme',
    );

    expect($config)->toBeInstanceOf(TrustedUserConfig::class)
        ->and($config->impersonatedUsername)->toBe('bob');
});
