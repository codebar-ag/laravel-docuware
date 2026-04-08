<?php

namespace CodebarAg\DocuWare\DTO\FileCabinets;

use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class Dialog
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        $fieldsRaw = Arr::get($data, 'Fields');
        $fields = null;
        if (is_array($fieldsRaw)) {
            $fields = JsonArrays::listOfRecords($fieldsRaw);
        }

        return new self(
            id: Arr::get($data, 'Id'),
            type: Arr::get($data, 'Type'),
            label: Arr::get($data, 'DisplayName'),
            isDefault: Arr::get($data, 'IsDefault'),
            fileCabinetId: Arr::get($data, 'FileCabinetId'),
            fields: $fields,
        );
    }

    /**
     * @param  list<array<string, mixed>>|null  $fields
     */
    public function __construct(
        public string $id,
        public string $type,
        public string $label,
        public bool $isDefault,
        public string $fileCabinetId,
        public ?array $fields = null,
    ) {}

    public function isSearch(): bool
    {
        return $this->type === 'Search';
    }

    public static function fake(
        ?string $id = null,
        ?string $type = null,
        ?string $label = null,
        ?bool $isDefault = null,
        ?string $fileCabinetId = null,
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
