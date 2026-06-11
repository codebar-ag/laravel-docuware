<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetUsersOfAGroup extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public ?string $groupId = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/Organization/GroupUsers';
    }

    protected function defaultQuery(): array
    {
        return [
            'GroupId' => $this->groupId,
        ];
    }
}
