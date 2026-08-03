<?php

use CodebarAg\DocuWare\Config\InstanceConfig;
use CodebarAg\DocuWare\Data\Authentication\RequestTokenData;
use CodebarAg\DocuWare\Transport\Auth\EncryptedCacheTokenRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

function expiryToken(string $access = 'access', int $expiresIn = 3600): RequestTokenData
{
    return new RequestTokenData(
        accessToken: $access,
        tokenType: 'Bearer',
        scope: 'docuware.platform',
        expiresIn: $expiresIn,
        expiresAt: Carbon::now()->addSeconds($expiresIn),
    );
}

function expiryConfig(string $name = 'default'): InstanceConfig
{
    return InstanceConfig::make($name, [
        'grant' => 'credentials',
        'url' => 'https://example.docuware.cloud',
        'username' => 'alice',
        'password' => 'secret',
        'cacheDriver' => 'array',
    ]);
}

afterEach(fn () => Carbon::setTestNow());

it('re-fetches once the cached token has expired', function () {
    $config = expiryConfig();
    $repo = new EncryptedCacheTokenRepository;

    Carbon::setTestNow(Carbon::parse('2024-01-01 12:00:00'));
    $repo->accessToken($config, fn () => expiryToken('first', 3600));

    // Move past the cached TTL (lifetime minus the 60s skew).
    Carbon::setTestNow(Carbon::parse('2024-01-01 13:00:01'));

    $calls = 0;
    $token = $repo->accessToken($config, function () use (&$calls) {
        $calls++;

        return expiryToken('second', 3600);
    });

    expect($token)->toBe('second')->and($calls)->toBe(1);
});

it('recovers from a corrupted cache entry by re-fetching', function () {
    $config = expiryConfig();
    $repo = new EncryptedCacheTokenRepository;

    // A value that is not valid Crypt ciphertext — decryption must fail gracefully, not throw.
    Cache::store('array')->put('docuware.oauth.'.$config->identifier(), 'not-encrypted-garbage', 3600);

    $calls = 0;
    $token = $repo->accessToken($config, function () use (&$calls) {
        $calls++;

        return expiryToken('recovered');
    });

    expect($token)->toBe('recovered')->and($calls)->toBe(1);
});

it('still caches a short-lived token whose skew underflows (TTL clamped to >= 1s)', function () {
    $config = expiryConfig();
    $repo = new EncryptedCacheTokenRepository;

    Carbon::setTestNow(Carbon::parse('2024-01-01 12:00:00'));
    $repo->accessToken($config, fn () => expiryToken('short', 30)); // 30 - 60 skew -> clamped to 1s TTL

    $calls = 0;
    $token = $repo->accessToken($config, function () use (&$calls) {
        $calls++;

        return expiryToken('again', 30);
    });

    // Immediate second read (same frozen instant) is a cache hit — no re-fetch.
    expect($token)->toBe('short')->and($calls)->toBe(0);
});
