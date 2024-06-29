<?php

namespace CodebarAg\DocuWare\DTO\General\UserManagement\CreateUpdateUser;

use Illuminate\Support\Arr;

final class User
{
    public static function fromJson(array $data): self
    {
        return new self(
            name: Arr::get($data, 'Name'),
            dbName: Arr::get($data, 'DBName'),
            email: Arr::get($data, 'EMail'),
            password: Arr::get($data, 'Password'),
            networkId: Arr::get($data, 'NetworkId'),
        );
    }

    public function __construct(
        public string $name,
        public string $dbName,
        public string $email,
        public string $password,
        public ?string $networkId = null,
    ) {}

    public static function fake(
        ?string $name = null,
        ?string $dbName = null,
        ?string $email = null,
        ?string $networkId = null,
        ?string $password = null,
    ): self {
        return new self(
            name: $name ?? (string) 'Fake File Cabinet',
            dbName: $dbName ?? (string) 'Fake DB Name',
            email: $email ?? (string) 'test@example.com',
            networkId: $networkId ?? (string) '',
            password: $password ?? (string) 'Fake Password',
        );
    }
}
