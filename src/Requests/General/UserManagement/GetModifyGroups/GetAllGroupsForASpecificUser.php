<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyGroups\Group;
use CodebarAg\DocuWare\Responses\General\UserManagement\GetModifyGroups\GetGroupsResponse;
use Illuminate\Support\Collection;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetAllGroupsForASpecificUser extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public string $userId,
        public ?string $name = null,
        public ?bool $active = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/Organization/UserGroups';
    }

    protected function defaultQuery(): array
    {
        return [
            'UserId' => $this->userId,
            'Name' => $this->name,
            'Active' => $this->active,
        ];
    }

    /**
     * @return Collection<int, Group>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return GetGroupsResponse::fromResponse($response);
    }
}
