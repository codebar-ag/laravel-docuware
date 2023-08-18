<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class Organization
{
    public static function fromJson(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            name: Arr::get($data, 'Name'),
            guid: Arr::get($data, 'Guid'),
            additionalInfo: Arr::get($data, 'AdditionalInfo', []),
            configurationRights: Arr::get($data, 'ConfigurationRights', []),
        );
    }

    public function __construct(
        public string $id,
        public string $name,
        public ?string $guid = null,
        public array $additionalInfo = [],
        public array $configurationRights = [],
    ) {
    }

    public static function fake(
        string $id = null,
        string $name = null,
        string $guid = null,
        array $additionalInfo = [],
        array $configurationRights = [],
    ): self {
        return new self(
            id: $id ?? (string) Str::uuid(),
            name: $name ?? 'Fake File Cabinet',
            guid: $guid ?? (string) Str::uuid(),
            additionalInfo: $additionalInfo,
            configurationRights: $configurationRights,
        );
    }
}
