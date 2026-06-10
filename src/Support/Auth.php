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
        /** @var list<array<string, mixed>> $cookieList */
        $cookieList = $cookies->toArray();

        $cookie = collect($cookieList)
            ->reject(fn (array $cookie) => Arr::get($cookie, 'Value') === '')
            ->firstWhere('Name', self::COOKIE_NAME);

        Cache::driver(self::cacheDriver())
            ->put(
                self::CACHE_KEY,
                [
                    Arr::get($cookie, 'Name') => Arr::get($cookie, 'Value'),
                    'CreatedAt' => now()->toDateTimeString(),
                ],
                now()->addMinutes(config('laravel-docuware.cookie_lifetime')),
            );
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function cookies(): ?array
    {
        $cached = Cache::driver(self::cacheDriver())->get(self::CACHE_KEY);

        return is_array($cached) ? $cached : null;
    }

    public static function cookieJar(): ?CookieJar
    {
        if (! self::cookies()) {
            return null;
        }

        return CookieJar::fromArray(self::cookies(), self::domain());
    }

    public static function cookieDate(): string
    {
        $cached = Cache::driver(self::cacheDriver())->get(self::CACHE_KEY);

        return is_array($cached) ? (string) Arr::get($cached, 'CreatedAt') : '';
    }

    public static function forget(): void
    {
        Cache::driver(self::cacheDriver())->forget(self::CACHE_KEY);
    }

    public static function domain(): string
    {
        throw_if(
            blank(config('laravel-docuware.credentials.url')),
            UnableToFindUrlCredential::create(),
        );

        return Str::of(config('laravel-docuware.credentials.url'))
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
        return config('laravel-docuware.cache_driver', self::FALLBACK_CACHE_DRIVER);
    }
}
