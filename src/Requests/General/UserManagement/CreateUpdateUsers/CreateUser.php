<?php

namespace CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\General\UserManagement\CreateUpdateUser\User;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateUser extends Request implements Cacheable, HasBody
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
            'Content-Type' => 'application/vnd.docuware.platform.createorganizationuser+json',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'Name' => $this->user->name,
            'DbName' => $this->user->dbName,
            'Email' => $this->user->email,
            'NetworkId' => $this->user->networkId,
            'Password' => $this->user->password,
        ];
    }
}
