<?php

namespace CodebarAg\DocuWare\DTO;

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
}
