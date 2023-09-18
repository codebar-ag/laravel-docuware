<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;

final class Config
{
    public static function make(array $data): self
    {
        return new self(
            url: Arr::get($data, 'url'),
            cookie: Arr::get($data, 'cookie'),
            cacheDriver: Arr::get($data, 'cache_driver'),
            cacheLifetimeInSeconds: Arr::get($data, 'cache_lifetime_in_seconds'),
            requestTimeoutInSeconds: Arr::get($data, 'request_timeout_in_seconds'),
        );
    }

    public function __construct(
        public string $url,
        public string $cookie,
        public string $cacheDriver,
        public int $cacheLifetimeInSeconds,
        public string $requestTimeoutInSeconds,
    ) {
    }
}
