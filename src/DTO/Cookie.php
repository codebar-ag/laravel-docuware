<?php

namespace CodebarAg\DocuWare\DTO;

use CodebarAg\DocuWare\Exceptions\UnableToLoginNoCookies;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

final class Cookie
{
    public function __construct(
        public string $cookie,
        public Carbon $cookie_created_at,
        public Carbon $cookie_lifetime_until,
    ) {
    }

    public static function make(CookieJar $cookieJar): self
    {
        throw_if($cookieJar->toArray() === [], UnableToLoginNoCookies::create());

        $data = collect($cookieJar->toArray())
            ->reject(fn (array $cookie) => Arr::get($cookie, 'Value') === '')
            ->firstWhere('Name', '.DWPLATFORMAUTH');

        return new self(
            cookie: Arr::get($data, 'Value'),
            cookie_created_at: now(),
            cookie_lifetime_until: now()->addMinutes(525600 * 0.75),
        );
    }
}
