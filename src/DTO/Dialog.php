<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class Dialog
{
    public static function fromJson(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            type: Arr::get($data, 'Type'),
            label: Arr::get($data, 'DisplayName'),
            isDefault: Arr::get($data, 'IsDefault'),
            fileCabinetId: Arr::get($data, 'FileCabinetId'),
            fields: Arr::get($data, 'Fields'),
        );
    }

    public function __construct(
        public string $id,
        public string $type,
        public string $label,
        public bool $isDefault,
        public string $fileCabinetId,
        public ?array $fields = null,
    ) {
    }

    public function isSearch(): bool
    {
        return $this->type === 'Search';
    }

    public static function fake(
        string $id = null,
        string $type = null,
        string $label = null,
        bool $isDefault = null,
        string $fileCabinetId = null,
    ): self {
        return new self(
            id: $id ?? (string) Str::uuid(),
            type: $type ?? Arr::random(['Search', 'Store', 'ResultList', 'InfoDialog']),
            label: $label ?? 'Fake Dialog',
            isDefault: $isDefault ?? Arr::random([true, false]),
            fileCabinetId: $fileCabinetId ?? (string) Str::uuid(),
        );
    }
}
