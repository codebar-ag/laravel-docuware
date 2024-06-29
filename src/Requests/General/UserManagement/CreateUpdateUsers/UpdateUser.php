<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers;

use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
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

class UpdateUser extends Request implements Cacheable, HasBody
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
            'Content-Type' => 'application/json',
        ];
    }

    protected function defaultBody(): array
    {
        return [
            'Id' => $this->user->id,
            'Active' => $this->user->active,
            'FirstName' => $this->user->firstName,
            'LastName' => $this->user->lastName,
            'Salutation' => $this->user->salutation,
            'Name' => $this->user->name,
            'Email' => $this->user->email,
            'OutOfOffice' => [
                'IsOutOfOffice' => $this->user->outOfOffice->isOutOfOffice,
                'StartDateTime' => $this->user->outOfOffice->startDateTime?->toISOString(),
                'EndDateTime' => $this->user->outOfOffice->endDateTime?->toISOString(),
            ],
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
