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

class GetUsers extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public ?string $name = null,
        public ?bool $active = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/Organization/Users';
    }

    protected function defaultQuery(): array
    {
        return [
            'Name' => $this->name,
            'Active' => $this->active,
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
