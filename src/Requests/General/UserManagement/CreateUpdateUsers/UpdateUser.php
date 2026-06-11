<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use CodebarAg\DocuWare\Responses\General\UserManagement\GetUsers\GetUserResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class UpdateUser extends Request implements Cacheable, HasBody
{
    use HasDocuWareCaching;
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly User $user,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/Organization/UserInfo';
    }

    public function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        $outOfOffice = $this->user->outOfOffice;

        return [
            'Id' => $this->user->id,
            'Active' => $this->user->active,
            'FirstName' => $this->user->firstName,
            'LastName' => $this->user->lastName,
            'Salutation' => $this->user->salutation,
            'Name' => $this->user->name,
            'Email' => $this->user->email,
            'OutOfOffice' => [
                'IsOutOfOffice' => $outOfOffice !== null && $outOfOffice->isOutOfOffice,
                'StartDateTime' => $outOfOffice?->startDateTime?->toISOString(),
                'EndDateTime' => $outOfOffice?->endDateTime?->toISOString(),
            ],
        ];
    }

    public function createDtoFromResponse(Response $response): User
    {
        return GetUserResponse::fromResponse($response);
    }
}
