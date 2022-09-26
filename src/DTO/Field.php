<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;

class Field
{
    public static function fromJson(array $data): self
    {
        return new static(
            name: $data['DBFieldName'],
            label: $data['DisplayName'],
            type: $data['DWFieldType'],
            scope: $data['Scope'],
        );
    }

    public function __construct(
        public string $name,
        public string $label,
        public string $type,
        public string $scope,
    ) {
    }

    public function isSystem(): bool
    {
        return $this->scope === 'System';
    }

    public function isUser(): bool
    {
        return $this->scope === 'User';
    }

    public static function fake(
        ?string $name = null,
        ?string $label = null,
        ?string $type = null,
        ?string $scope = null,
    ): self {
        return new static(
            name: $name ?? 'FAKE_FIELD',
            label: $label ?? 'Fake Field',
            type: Arr::random(['Text', 'Memo', 'Numeric', 'Decimal', 'Date', 'DateTime', 'Keyword']),
            scope: $scope ?? Arr::random(['System', 'User']),
        );
    }
}
