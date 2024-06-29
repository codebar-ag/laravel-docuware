<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers;

use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use CodebarAg\DocuWare\Responses\General\UserManagement\GetUsers\GetUserResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetUserById extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public string $userId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/Organization/UserByID';
    }

    protected function defaultQuery(): array
    {
        return [
            'userId' => $this->userId,
        ];
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('laravel-docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('laravel-docuware.configurations.cache.lifetime_in_seconds', 3600);
    }

    public function createDtoFromResponse(Response $response): User
    {
        return GetUserResponse::fromResponse($response);
    }
}
