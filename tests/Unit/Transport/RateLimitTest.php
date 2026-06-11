<?php

use CodebarAg\DocuWare\Data\Authentication\RequestTokenData;
use CodebarAg\DocuWare\DocuWareManager;
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\RateLimitPlugin\Exceptions\RateLimitReachedException;

beforeEach(function () {
    config()->set('laravel-docuware.default', 'default');
    config()->set('laravel-docuware.instances.default', [
        'grant' => 'credentials',
        'url' => 'https://one.docuware.cloud',
        'username' => 'alice',
        'password' => 'secret',
    ]);
    config()->set('laravel-docuware.instances.other', [
        'grant' => 'credentials',
        'url' => 'https://two.docuware.cloud',
        'username' => 'bob',
        'password' => 'secret',
    ]);
    config()->set('laravel-docuware.configurations.cache.driver', 'array');
});

function seedRateToken(string $instance): void
{
    $config = app(DocuWareManager::class)->resolveConfig($instance);

    $token = new RequestTokenData(
        accessToken: 'seeded',
        tokenType: 'Bearer',
        scope: 'docuware.platform',
        expiresIn: 3600,
        expiresAt: Carbon::now()->addHour(),
    );

    Cache::store('array')->put('docuware.oauth.'.$config->identifier(), Crypt::encrypt($token), 3600);
}

function rateLimitedConnector(string $instance)
{
    seedRateToken($instance);

    return app(DocuWareManager::class)->instance($instance)->connector()
        ->withMockClient(new MockClient([
            GetOrganization::class => MockResponse::fixture('get-organization'),
        ]));
}

it('does not throttle when rate limiting is disabled (the default)', function () {
    $connector = rateLimitedConnector('default');

    for ($i = 0; $i < 5; $i++) {
        expect($connector->send(new GetOrganization)->status())->toBe(200);
    }
});

it('throttles outgoing requests once the configured limit is reached', function () {
    config()->set('laravel-docuware.rate_limit', ['enabled' => true, 'allow' => 2, 'per_seconds' => 60]);

    $connector = rateLimitedConnector('default');

    $connector->send(new GetOrganization);
    $connector->send(new GetOrganization);

    expect(fn () => $connector->send(new GetOrganization))
        ->toThrow(RateLimitReachedException::class);
});

it('gives each instance its own budget (tenant isolation)', function () {
    config()->set('laravel-docuware.rate_limit', ['enabled' => true, 'allow' => 1, 'per_seconds' => 60]);

    $default = rateLimitedConnector('default');
    $other = rateLimitedConnector('other');

    $default->send(new GetOrganization);
    expect(fn () => $default->send(new GetOrganization))->toThrow(RateLimitReachedException::class);

    // A different instance has a separate bucket and is unaffected.
    expect($other->send(new GetOrganization)->status())->toBe(200);
});
