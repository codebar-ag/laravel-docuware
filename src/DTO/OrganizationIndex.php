<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class OrganizationIndex
{
    public static function fromJson(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            name: Arr::get($data, 'Name'),
            guid: Arr::get($data, 'Guid'),
        );
    }

    public function __construct(
        public string $id,
        public string $name,
        public ?string $guid = null,
    ) {
    }

    public static function fake(
        string $id = null,
        string $name = null,
        string $guid = null,
    ): self {
        return new self(
            id: $id ?? (string) Str::uuid(),
            name: $name ?? 'Fake File Cabinet',
            guid: $guid ?? (string) Str::uuid(),
        );
    }
}
