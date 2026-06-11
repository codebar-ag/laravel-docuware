<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use CodebarAg\DocuWare\Responses\General\UserManagement\GetUsers\GetUserResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetUserById extends Request implements Cacheable
{
    use HasDocuWareCaching;

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

    public function createDtoFromResponse(Response $response): User
    {
        return GetUserResponse::fromResponse($response);
    }
}
