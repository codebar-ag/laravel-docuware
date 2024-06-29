<?php

namespace CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyRoles;

use Illuminate\Support\Arr;

final class Role
{
    public static function fromJson(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            name: Arr::get($data, 'Name'),
            active: Arr::get($data, 'Active'),
            type: Arr::get($data, 'Type'),
        );
    }

    public function __construct(
        public string $id,
        public string $name,
        public bool $active,
        public string $type,
    ) {
    }
}
