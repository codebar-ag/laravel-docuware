<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers;

use CodebarAg\DocuWare\DTO\General\UserManagement\CreateUpdateUser\User;
use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User as GetUser;
use CodebarAg\DocuWare\Responses\General\UserManagement\GetUsers\GetUserResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class CreateUser extends Request implements Cacheable, HasBody
{
    use HasCaching;
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly User $user,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/Organization/UserInfo';
    }

    public function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/vnd.docuware.platform.createorganizationuser+json',
        ];
    }

    protected function defaultBody(): array
    {
        return [
            'Name' => $this->user->name,
            'DbName' => $this->user->dbName,
            'Email' => $this->user->email,
            'NetworkId' => $this->user->networkId,
            'Password' => $this->user->password,
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

    public function createDtoFromResponse(Response $response): GetUser
    {
        return GetUserResponse::fromResponse($response);
    }
}
