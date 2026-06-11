<?php

namespace CodebarAg\DocuWare\Data\FileCabinets;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\LinkData;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class DialogData extends DocuWareData
{
    /**
     * @param  list<array<string, mixed>>|null  $fields
     * @param  Collection<int, LinkData>|null  $links
     */
    public function __construct(
        public string $id,
        public string $type,
        public string $label,
        public bool $isDefault,
        public string $fileCabinetId,
        public ?array $fields = null,
        public ?string $schemaType = null,
        public ?Collection $links = null,
        public ?string $fileCabinetName = null,
        public ?bool $isForMobile = null,
        public ?string $assignedDialogId = null,
        public ?string $color = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
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
            links: LinkData::collection(Arr::get($data, 'Links')),
            fileCabinetName: Arr::get($data, 'FileCabinetName'),
            isForMobile: Arr::get($data, 'IsForMobile'),
            assignedDialogId: Arr::get($data, 'AssignedDialogId'),
            color: Arr::get($data, 'Color'),
        );
    }

    public function isSearch(): bool
    {
        return $this->type === 'Search';
    }

    /**
     * The dialog's fields as typed {@see DialogFieldData} objects (parsed from the raw `$fields`).
     *
     * @return Collection<int, DialogFieldData>
     */
    public function fieldObjects(): Collection
    {
        return collect($this->fields ?? [])
            ->map(fn (array $field): DialogFieldData => DialogFieldData::fromDocuWare($field))
            ->values();
    }
}
