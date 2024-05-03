<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers;

use CodebarAg\DocuWare\Responses\General\UserManagement\GetUsers\GetUsersResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetUsersOfARole extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public null|string $roleId = null,
        public null|bool $includeGroupUsers = null,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/Organization/RoleUsers';
    }

    protected function defaultQuery(): array
    {
        return [
            'RoleId' => $this->roleId,
            'IncludeGroupUsers' => $this->includeGroupUsers,
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

    public function createDtoFromResponse(Response $response): Enumerable|Collection
    {
        return GetUsersResponse::fromResponse($response);
    }
}
