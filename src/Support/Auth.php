<?php

namespace CodebarAg\DocuWare\Support;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\Cache;

class Auth
{
    const COOKIE_NAME = '.DWPLATFORMAUTH';

    public static function store(CookieJar $cookies): void
    {
        $cookie = collect($cookies->toArray())
            ->reject(fn (array $cookie) => $cookie['Value'] === '')
            ->firstWhere('Name', self::COOKIE_NAME);

        Cache::put(
            'docuware.cookies',
            [$cookie['Name'] => $cookie['Value']],
            now()->addMinutes(config('docuware.cookie_lifetime')),
        );
    }

    public static function cookies(): ?array
    {
        return Cache::get('docuware.cookies');
    }

    public static function forget(): void
    {
        Cache::forget('docuware.cookies');
    }

    public static function domain(): string
    {
        return ParseValue::domain();
    }
}
