<?php

namespace CodebarAg\DocuWare\Support;

use CodebarAg\DocuWare\Exceptions\UnableToFindUrlCredential;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Auth
{
    const COOKIE_NAME = '.DWPLATFORMAUTH';

    const CACHE_KEY = 'docuware.cookies';

    const FALLBACK_CACHE_DRIVER = 'file';

    public static function store(CookieJar $cookies): void
    {
        $cookie = collect($cookies->toArray())
            ->reject(fn (array $cookie) => $cookie['Value'] === '')
            ->firstWhere('Name', self::COOKIE_NAME);

        Cache::driver(self::cacheDriver())
            ->put(
                self::CACHE_KEY,
                [
                    $cookie['Name'] => $cookie['Value'],
                    'CreatedAt' => now()->toDateTimeString(),
                ],
                now()->addMinutes(config('docuware.cookie_lifetime')),
            );
    }

    public static function cookies(): ?array
    {
        if (config('docuware.cookies')) {
            return [self::COOKIE_NAME => config('docuware.cookies')];
        }

        return Cache::driver(self::cacheDriver())->get(self::CACHE_KEY);
    }

    public static function cookieDate(): string
    {
        return Arr::get(Cache::driver(self::cacheDriver())->get(self::CACHE_KEY), 'CreatedAt');
    }

    public static function forget(): void
    {
        Cache::driver(self::cacheDriver())->forget(self::CACHE_KEY);
    }

    public static function domain(): string
    {
        throw_if(
            empty(config('docuware.credentials.url')),
            UnableToFindUrlCredential::create(),
        );

        return Str::of(config('docuware.credentials.url'))
            ->after('//')
            ->beforeLast('/')
            ->__toString();
    }

    public static function check(): bool
    {
        return Cache::driver(self::cacheDriver())->has(self::CACHE_KEY);
    }

    protected static function cacheDriver(): string
    {
        return config('docuware.cache_driver', self::FALLBACK_CACHE_DRIVER);
    }
}
