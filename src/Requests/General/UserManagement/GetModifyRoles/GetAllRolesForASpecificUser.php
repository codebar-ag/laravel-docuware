<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetAllRolesForASpecificUser extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public string $userId,
        public ?string $name = null,
        public ?bool $active = null,
        public ?string $type = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/Organization/UserRoles';
    }

    protected function defaultQuery(): array
    {
        return [
            'UserId' => $this->userId,
            'Name' => $this->name,
            'Active' => $this->active,
            'Type' => $this->type,
        ];
    }
}
