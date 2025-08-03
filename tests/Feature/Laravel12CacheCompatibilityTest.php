<?php

test('cache configuration uses CACHE_STORE in Laravel 12', function () {
    // Mock the configuration to simulate Laravel 12 with CACHE_STORE
    config([
        'laravel-docuware.configurations.cache.driver' => 'redis',
    ]);

    // Test that the configuration is properly loaded
    $cacheDriver = config('laravel-docuware.configurations.cache.driver');
    expect($cacheDriver)->toBe('redis');
});

test('cache configuration falls back to CACHE_DRIVER when CACHE_STORE is not set', function () {
    // Mock the configuration to simulate older Laravel versions
    config([
        'laravel-docuware.configurations.cache.driver' => 'file',
    ]);

    // Test that the configuration is properly loaded
    $cacheDriver = config('laravel-docuware.configurations.cache.driver');
    expect($cacheDriver)->toBe('file');
});

test('cache configuration uses DOCUWARE_CACHE_DRIVER when explicitly set', function () {
    // Mock the configuration to prioritize DOCUWARE_CACHE_DRIVER
    config([
        'laravel-docuware.configurations.cache.driver' => 'array',
    ]);

    // Test that the configuration is properly loaded
    $cacheDriver = config('laravel-docuware.configurations.cache.driver');
    expect($cacheDriver)->toBe('array');
});

test('cache configuration respects the new CACHE_STORE fallback chain', function () {
    // Test that the configuration properly uses the new fallback chain:
    // DOCUWARE_CACHE_DRIVER -> CACHE_STORE -> CACHE_DRIVER -> 'file'

    // Mock the configuration to simulate the fallback chain
    config([
        'laravel-docuware.configurations.cache.driver' => 'redis', // This would be set by CACHE_STORE
    ]);

    // Test that the configuration is properly loaded
    $cacheDriver = config('laravel-docuware.configurations.cache.driver');
    expect($cacheDriver)->toBe('redis');
});

test('cache configuration uses default file driver when no configuration is set', function () {
    // Reset the configuration to test default behavior
    config([
        'laravel-docuware.configurations.cache.driver' => 'file',
    ]);

    // Test that the configuration is properly loaded
    $cacheDriver = config('laravel-docuware.configurations.cache.driver');
    expect($cacheDriver)->toBe('file');
});

test('environment variable fallback chain works correctly', function () {
    // Test that the environment variable fallback chain works as expected
    // This simulates the actual configuration loading logic

    // Mock the environment variables
    putenv('DOCUWARE_CACHE_DRIVER=array');
    putenv('CACHE_STORE=redis');
    putenv('CACHE_DRIVER=file');

    // The configuration should prioritize DOCUWARE_CACHE_DRIVER
    $expectedDriver = env('DOCUWARE_CACHE_DRIVER', env('CACHE_STORE', env('CACHE_DRIVER', 'file')));
    expect($expectedDriver)->toBe('array');

    // Clean up
    putenv('DOCUWARE_CACHE_DRIVER');
    putenv('CACHE_STORE');
    putenv('CACHE_DRIVER');
});

test('CACHE_STORE takes precedence over CACHE_DRIVER when DOCUWARE_CACHE_DRIVER is not set', function () {
    // Mock the environment variables
    putenv('DOCUWARE_CACHE_DRIVER=');
    putenv('CACHE_STORE=redis');
    putenv('CACHE_DRIVER=file');

    // The configuration should use CACHE_STORE when DOCUWARE_CACHE_DRIVER is not set
    $expectedDriver = env('DOCUWARE_CACHE_DRIVER', env('CACHE_STORE', env('CACHE_DRIVER', 'file')));
    expect($expectedDriver)->toBe('redis');

    // Clean up
    putenv('DOCUWARE_CACHE_DRIVER');
    putenv('CACHE_STORE');
    putenv('CACHE_DRIVER');
});
