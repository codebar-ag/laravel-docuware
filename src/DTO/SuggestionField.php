<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;

final class SuggestionField
{
    public static function fromJson(array $data): self
    {
        return new self(
            value: Arr::get($data, 'Value', []),
            name: Arr::get($data, 'Name'),
            db_name: Arr::get($data, 'DBName'),
            confidence: Arr::get($data, 'Confidence'),
        );
    }

    public function __construct(
        public array $value,
        public ?string $name,
        public ?string $db_name,
        public ?string $confidence,
    ) {
    }

    public static function fake(
        array $value = [],
        string $name = null,
        string $db_name = null,
        string $confidence = null,
    ): self {
        return new self(
            value: $value,
            name: $name ?? 'Fake Name',
            db_name: $db_name ?? 'FAKE_NAME',
            confidence: $confidence ?? 'Red',
        );
    }
}
