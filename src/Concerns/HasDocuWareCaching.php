<?php

namespace CodebarAg\DocuWare\Concerns;

use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;

/**
 * Shared caching configuration for cacheable DocuWare requests.
 *
 * Pairs with Saloon's Cacheable contract: the request keeps
 * `implements Cacheable` and gains the driver + TTL resolved from the
 * package's `laravel-docuware.configurations.cache.*` config.
 */
trait HasDocuWareCaching
{
    use HasCaching;

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('laravel-docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('laravel-docuware.configurations.cache.lifetime_in_seconds', 3600);
    }
}
