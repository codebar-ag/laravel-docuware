<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles;

use CodebarAg\DocuWare\Responses\General\UserManagement\GetModifyRoles\GetRolesResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetRoles extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public null|string $name = null,
        public null|bool $active = null,
        public null|string $type = null,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/Organization/Roles';
    }

    protected function defaultQuery(): array
    {
        return [
            'Name' => $this->name,
            'Active' => $this->active,
            'Type' => $this->type,
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
        return GetRolesResponse::fromResponse($response);
    }
}
