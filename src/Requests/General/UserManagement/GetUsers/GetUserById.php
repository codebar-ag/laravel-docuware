<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;

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
}
