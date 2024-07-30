<?php

namespace CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyGroups;

use Illuminate\Support\Arr;

final class Group
{
    public static function fromJson(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            name: Arr::get($data, 'Name'),
            active: Arr::get($data, 'Active'),
        );
    }

    public function __construct(
        public string $id,
        public string $name,
        public bool $active,
    ) {}
}
