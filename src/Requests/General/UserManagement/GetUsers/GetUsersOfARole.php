<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use CodebarAg\DocuWare\Responses\General\UserManagement\GetUsers\GetUsersResponse;
use Illuminate\Support\Collection;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetUsersOfARole extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public ?string $roleId = null,
        public ?bool $includeGroupUsers = null,
    ) {}

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

    /**
     * @return Collection<int, User>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return GetUsersResponse::fromResponse($response);
    }
}
