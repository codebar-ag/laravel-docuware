<?php

namespace CodebarAg\DocuWare\DTO\FileCabinets;

use CodebarAg\DocuWare\DTO\Link;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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
            schemaType: Arr::get($data, '$type'),
            links: Link::collection(Arr::get($data, 'Links')),
            fileCabinetName: Arr::get($data, 'FileCabinetName'),
            isForMobile: Arr::get($data, 'IsForMobile'),
            assignedDialogId: Arr::get($data, 'AssignedDialogId'),
            color: Arr::get($data, 'Color'),
        );
    }

    /**
     * @param  list<array<string, mixed>>|null  $fields
     * @param  Collection<int, Link>|null  $links
     */
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $label,
        public readonly bool $isDefault,
        public readonly string $fileCabinetId,
        public readonly ?array $fields = null,
        public readonly ?string $schemaType = null,
        public readonly ?Collection $links = null,
        public readonly ?string $fileCabinetName = null,
        public readonly ?bool $isForMobile = null,
        public readonly ?string $assignedDialogId = null,
        public readonly ?string $color = null,
    ) {}

    public function isSearch(): bool
    {
        return $this->type === 'Search';
    }

    /**
     * The dialog's fields as typed {@see DialogField} objects (parsed from the raw `$fields`).
     *
     * @return Collection<int, DialogField>
     */
    public function fieldObjects(): Collection
    {
        return collect($this->fields ?? [])
            ->map(fn (array $field): DialogField => DialogField::fromJson($field))
            ->values();
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
