<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;

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
}
