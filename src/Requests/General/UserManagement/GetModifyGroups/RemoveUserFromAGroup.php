<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class RemoveUserFromAGroup extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    /**
     * @param  list<string>  $ids
     */
    public function __construct(
        public string $userId,
        public array $ids
    ) {}

    public function resolveEndpoint(): string
    {
        return '/Organization/UserGroups';
    }

    protected function defaultQuery(): array
    {
        return [
            'UserId' => $this->userId,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'Ids' => $this->ids,
            'OperationType' => 'Remove',
        ];
    }
}
