<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class AddUserToAGroup extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

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

    protected function defaultBody(): array
    {
        return [
            'Ids' => $this->ids,
            'OperationType' => 'Add',
        ];
    }

    public function createDtoFromResponse(Response $response): Response
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response;
    }
}
