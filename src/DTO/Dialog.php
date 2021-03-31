<?php

namespace CodebarAg\DocuWare\DTO;

class Dialog
{
    public static function fromJson(array $data): self
    {
        return new static(
            id: $data['Id'],
            type: $data['Type'],
            label: $data['DisplayName'],
            isDefault: $data['IsDefault'],
            fileCabinetId: $data['FileCabinetId'],
        );
    }

    public function __construct(
        public string $id,
        public string $type,
        public string $label,
        public bool $isDefault,
        public string $fileCabinetId,
    ) {
    }

    public function isSearch(): bool
    {
        return $this->type === 'Search';
    }
}
