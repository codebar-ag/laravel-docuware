<?php

use CodebarAg\DocuWare\Data\Organization\OrganizationData;
use CodebarAg\DocuWare\DocuWareManager;
use CodebarAg\DocuWare\Facades\DocuWare;
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;
use CodebarAg\DocuWare\Resources\OrganizationsResource;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    config()->set('laravel-docuware.default', 'default');
    config()->set('laravel-docuware.instances.default', [
        'grant' => 'credentials',
        'url' => 'https://example.docuware.cloud',
        'username' => 'alice',
        'password' => 'secret',
    ]);
    config()->set('laravel-docuware.configurations.cache.driver', 'array');
    config()->set('laravel-docuware.configurations.cache.lifetime_in_seconds', 60);
    config()->set('laravel-docuware.configurations.request.timeout_in_seconds', 30);
    config()->set('laravel-docuware.configurations.client_id', 'docuware.platform.net.client');
    config()->set('laravel-docuware.configurations.scope', 'docuware.platform');
});

it('exposes an organizations resource via the facade', function () {
    expect(DocuWare::organizations())->toBeInstanceOf(OrganizationsResource::class);
});

it('lists organizations from a real fixture', function () {
    $client = clientWithMock([
        GetOrganization::class => MockResponse::fixture('get-organization'),
    ]);

    $organizations = $client->organizations()->all();

    expect($organizations)->not->toBeEmpty()
        ->and($organizations->first())->toBeInstanceOf(OrganizationData::class)
        ->and($organizations->first()->id)->toBeString()
        ->and($organizations->first()->name)->toBeString();
});

it('resolves the facade to the manager', function () {
    expect(DocuWare::getFacadeRoot())->toBeInstanceOf(DocuWareManager::class);
});
