<?php

namespace CodebarAg\DocuWare\DTO;

use CodebarAg\DocuWare\Support\Auth;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class Configuration
{
	const COOKIE_NAME = '.DWPLATFORMAUTH';

	public function __construct(
		public string    $url,
		public string    $username,
		public string    $password,
		public ?string   $passphrase,
		public ?string    $cookie,
		public ?CookieJar $cookie_jar,
		public int       $cookie_lifetime,
		public string       $timeout,
		public string    $cache_driver,
		public array    $configurations,
	)
	{
	}

	public static function fromJson(array $data): self
	{
		$url = Arr::get($data, 'url');
		$cookie = Arr::get($data, 'cookie');

		return new self(
			url: $url,
			username: Arr::get($data, 'username'),
			password: Arr::get($data, 'password'),
			passphrase: Arr::get($data, 'passphrase'),
			cookie: $cookie,
			cookie_jar: self::cookie_jar($cookie, $url),
			cookie_lifetime: Arr::get($data, 'cookie_lifetime'),
			timeout: Arr::get($data, 'timeout'),
			cache_driver: Arr::get($data, 'cache_driver'),
			configurations: Arr::get($data, 'configurations'),

		);
	}

	public static function fake(
		string    $url = null,
		string    $username = null,
		string    $password = null,
		?string    $passphrase = null,
		?string    $cookie = null,
		?CookieJar $cookie_jar = null,
		string       $timeout = null,
		string    $cache_driver = null,
		array     $configurations = [],
	): self
	{
		$url = $url ?? 'https://laravel.docuware.cloud';
		$cookie = $cookie ?? Str::random(64);

		return new self(
			url: $url,
			username: $username ?? 'api@docuware.cloud',
			password: $password ?? Str::random(16),
			passphrase: $password ?? Str::random(8),
			cookie: $cookie,
			cookie_jar: CookieJar::fromArray(
				[Auth::COOKIE_NAME => $cookie],
				$url
			),
			cookie_lifetime: $cookie_lifetime ?? 525600,
			timeout: $timeout ?? 15,
			cache_driver: $cache_driver ?? 'file',
			configurations: [],
		);
	}

	protected static function cookie_jar(string $cookie, string $url)
	{
		return CookieJar::fromArray([self::COOKIE_NAME => $cookie], $url);
	}
}
