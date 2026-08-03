<?php

use CodebarAg\DocuWare\Data\Authentication\RequestTokenData;
use CodebarAg\DocuWare\DocuWareManager;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;
use CodebarAg\DocuWare\Security\Redactor;
use CodebarAg\DocuWare\Transport\DocuWareConnector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;
use Saloon\Http\Faking\MockClient;
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

function seedToken(): void
{
    $config = app(DocuWareManager::class)->resolveConfig('default');

    $token = new RequestTokenData(
        accessToken: 'seeded-token',
        tokenType: 'Bearer',
        scope: 'docuware.platform',
        expiresIn: 3600,
        expiresAt: Carbon::now()->addHour(),
    );

    Cache::store('array')->put('docuware.oauth.'.$config->identifier(), Crypt::encrypt($token), 3600);
}

it('builds a native connector from the manager', function () {
    expect(app(DocuWareManager::class)->instance()->connector())
        ->toBeInstanceOf(DocuWareConnector::class);
});

it('sends a request through the native connector using the cached token', function () {
    seedToken();
    Event::fake([ResponseReceived::class]);

    $connector = app(DocuWareManager::class)->instance()->connector();
    $connector->withMockClient(new MockClient([
        GetOrganization::class => MockResponse::fixture('get-organization'),
    ]));

    $response = $connector->send(new GetOrganization);

    expect($response->status())->toBe(200)
        ->and($response->collect('Organization'))->not->toBeEmpty();

    Event::assertDispatched(ResponseReceived::class, function (ResponseReceived $event) {
        return $event->instance === 'default'
            && $event->method === 'GET'
            && $event->status === 200;
    });
});

it('configures retries from config', function () {
    config()->set('laravel-docuware.retry', [
        'enabled' => true,
        'times' => 5,
        'base_interval_ms' => 100,
        'max_interval_ms' => 5000,
    ]);

    $connector = app(DocuWareManager::class)->instance()->connector();

    expect($connector->tries)->toBe(5)
        ->and($connector->retryInterval)->toBe(100)
        ->and($connector->useExponentialBackoff)->toBeTrue();
});

it('redacts authorization headers in the response event payload', function () {
    seedToken();

    $captured = null;
    Event::listen(ResponseReceived::class, function (ResponseReceived $event) use (&$captured) {
        $captured = $event;
    });

    $connector = app(DocuWareManager::class)->instance()->connector();
    $connector->withMockClient(new MockClient([
        GetOrganization::class => MockResponse::make(['Organization' => []], 200, [
            'Authorization' => 'Bearer super-secret-token',
            'Content-Type' => 'application/json',
        ]),
    ]));

    $connector->send(new GetOrganization);

    expect($captured)->not->toBeNull();
    $flat = json_encode($captured->headers);
    expect($flat)->not->toContain('super-secret-token')
        ->and($flat)->toContain(Redactor::PLACEHOLDER);
});
