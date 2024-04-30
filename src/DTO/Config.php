<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;

final class Config
{
    public static function make(array $data): self
    {
        return new self(
            url: Arr::get($data, 'url'),
            username: Arr::get($data, 'username'),
            password: Arr::get($data, 'password'),
            passphrase: Arr::get($data, 'passphrase'),
            cacheDriver: Arr::get($data, 'cache_driver'),
            cacheLifetimeInSeconds: Arr::get($data, 'cache_lifetime_in_seconds'),
            requestTimeoutInSeconds: Arr::get($data, 'request_timeout_in_seconds'),
        );
    }

    public function __construct(
        public string $url,
        public string $username,
        public string $password,
        public string $passphrase,
        public string $cacheDriver,
        public int $cacheLifetimeInSeconds,
        public int $requestTimeoutInSeconds,
    ) {
    }
}
