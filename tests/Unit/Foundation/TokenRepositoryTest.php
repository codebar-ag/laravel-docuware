<?php

use CodebarAg\DocuWare\Config\InstanceConfig;
use CodebarAg\DocuWare\Data\Authentication\RequestTokenData;
use CodebarAg\DocuWare\Events\TokenRefreshed;
use CodebarAg\DocuWare\Transport\Auth\EncryptedCacheTokenRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;

function fakeToken(string $access = 'access-123', int $expiresIn = 3600): RequestTokenData
{
    return new RequestTokenData(
        accessToken: $access,
        tokenType: 'Bearer',
        scope: 'docuware.platform',
        expiresIn: $expiresIn,
        expiresAt: Carbon::now()->addSeconds($expiresIn),
    );
}

function credentialsConfig(string $name = 'default'): InstanceConfig
{
    return InstanceConfig::make($name, [
        'grant' => 'credentials',
        'url' => 'https://example.docuware.cloud',
        'username' => 'alice',
        'password' => 'secret',
        'cacheDriver' => 'array',
    ]);
}

it('fetches, encrypts and caches a token on a miss', function () {
    Event::fake([TokenRefreshed::class]);
    $config = credentialsConfig();
    $repo = new EncryptedCacheTokenRepository;

    $calls = 0;
    $token = $repo->accessToken($config, function () use (&$calls) {
        $calls++;

        return fakeToken();
    });

    expect($token)->toBe('access-123')
        ->and($calls)->toBe(1);

    // Cached value is encrypted (not the raw token object/string).
    $raw = Cache::store('array')->get('docuware.oauth.'.$config->identifier());
    expect($raw)->toBeString()->not->toContain('access-123');
    expect(Crypt::decrypt($raw))->toBeInstanceOf(RequestTokenData::class);

    Event::assertDispatched(TokenRefreshed::class);
});

it('returns the cached token without re-fetching on a hit', function () {
    $config = credentialsConfig();
    $repo = new EncryptedCacheTokenRepository;

    $repo->accessToken($config, fn () => fakeToken('first'));

    $calls = 0;
    $token = $repo->accessToken($config, function () use (&$calls) {
        $calls++;

        return fakeToken('second');
    });

    expect($token)->toBe('first')->and($calls)->toBe(0);
});

it('re-fetches after forget', function () {
    $config = credentialsConfig();
    $repo = new EncryptedCacheTokenRepository;

    $repo->accessToken($config, fn () => fakeToken('first'));
    $repo->forget($config);

    $token = $repo->accessToken($config, fn () => fakeToken('second'));

    expect($token)->toBe('second');
});

it('namespaces tokens per instance', function () {
    $repo = new EncryptedCacheTokenRepository;

    $repo->accessToken(credentialsConfig('tenant-a'), fn () => fakeToken('token-a'));
    $tokenB = $repo->accessToken(credentialsConfig('tenant-b'), fn () => fakeToken('token-b'));

    expect($tokenB)->toBe('token-b');
});
