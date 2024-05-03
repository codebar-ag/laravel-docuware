<?php

namespace CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class User
{
    public static function fromJson(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            name: Arr::get($data, 'Name'),
            firstName: Arr::get($data, 'FirstName'),
            lastName: Arr::get($data, 'LastName'),
            dbName: Arr::get($data, 'DBName'),
            email: Arr::get($data, 'EMail'),
            active: Arr::get($data, 'Active'),
            isHighSecurity: Arr::get($data, 'IsHighSecurity'),
            defaultWebBasket: Arr::get($data, 'DefaultWebBasket'),
            outOfOffice: Arr::has($data, 'OutOfOffice') ? OutOfOffice::fromJson(Arr::get($data, 'OutOfOffice')) : null,
            regionalSettings: Arr::has($data, 'RegionalSettings') ? RegionalSettings::fromJson(Arr::get($data, 'RegionalSettings')) : null,
        );
    }

    public function __construct(
        public string $id,
        public string $name,
        public null|string $firstName,
        public null|string $lastName,
        public string $dbName,
        public string $email,
        public bool $active,
        public bool $isHighSecurity,
        public string $defaultWebBasket,
        public null|OutOfOffice $outOfOffice,
        public null|RegionalSettings $regionalSettings,
    ) {
    }

    public static function fake(
        ?string $id = null,
        ?string $name = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $dbName = null,
        ?string $email = null,
        ?bool $active = null,
        ?bool $isHighSecurity = null,
        ?string $defaultWebBasket = null,
        ?OutOfOffice $outOfOffice = null,
        ?RegionalSettings $regionalSettings = null,
    ): self {
        return new self(
            id: $id ?? (string) Str::uuid(),
            name: $name ?? (string) 'Fake File Cabinet',
            firstName: $firstName ?? (string) 'Fake First Name',
            lastName: $lastName ?? (string) 'Fake Last Name',
            dbName: $dbName ?? (string) 'Fake DB Name',
            email: $email ?? (string) 'test@example.com',
            active: $active ?? false,
            isHighSecurity: $isHighSecurity ?? false,
            defaultWebBasket: $defaultWebBasket ?? Str::uuid(),
            outOfOffice: $outOfOffice ?? null,
            regionalSettings: $regionalSettings ?? null,
        );
    }
}
