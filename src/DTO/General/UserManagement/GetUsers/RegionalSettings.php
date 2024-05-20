<?php

namespace CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers;

use Illuminate\Support\Arr;

final class RegionalSettings
{
    public static function fromJson(array $data): self
    {
        return new self(
            language: Arr::get($data, 'Language'),
            culture: Arr::get($data, 'Culture'),
        );
    }

    public function __construct(
        public ?string $language = null,
        public ?string $culture = null,
    ) {
    }
}
