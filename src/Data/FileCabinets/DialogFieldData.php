<?php

namespace CodebarAg\DocuWare\Data\FileCabinets;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\LinkData;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * A single field exposed by a dialog. Shape follows the DocuWare "Get a Specific Dialog"
 * response (DBFieldName / DlgLabel / DWFieldType / …).
 */
final class DialogFieldData extends DocuWareData
{
    /**
     * @param  array<int|string, mixed>|null  $selectListsAssigned
     * @param  array<int|string, mixed>|null  $selectListInfos
     * @param  Collection<int, LinkData>|null  $links
     */
    public function __construct(
        public string $dbName,
        public string $label,
        public string $type,
        public int $length,
        public int $precision,
        public bool $locked,
        public bool $readOnly,
        public bool $notEmpty,
        public bool $visible,
        public bool $allowExtendedSearch,
        public bool $usedAsDocumentName,
        public bool $isHierarchy,
        public ?bool $allowFiltering = null,
        public ?bool $selectListOnly = null,
        public ?string $selectListType = null,
        public ?string $assignedInternalSelectList = null,
        public ?array $selectListsAssigned = null,
        public ?array $selectListInfos = null,
        public ?bool $calculateSum = null,
        public ?Collection $links = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            dbName: (string) Arr::get($data, 'DBFieldName', ''),
            label: (string) Arr::get($data, 'DlgLabel', ''),
            type: (string) Arr::get($data, 'DWFieldType', ''),
            length: (int) Arr::get($data, 'Length', 0),
            precision: (int) Arr::get($data, 'Precision', 0),
            locked: (bool) Arr::get($data, 'Locked', false),
            readOnly: (bool) Arr::get($data, 'ReadOnly', false),
            notEmpty: (bool) Arr::get($data, 'NotEmpty', false),
            visible: (bool) Arr::get($data, 'Visible', true),
            allowExtendedSearch: (bool) Arr::get($data, 'AllowExtendedSearch', false),
            usedAsDocumentName: (bool) Arr::get($data, 'UsedAsDocumentName', false),
            isHierarchy: (bool) Arr::get($data, 'IsHierarchy', false),
            allowFiltering: Arr::get($data, 'AllowFiltering'),
            selectListOnly: Arr::get($data, 'SelectListOnly'),
            selectListType: Arr::get($data, 'SelectListType'),
            assignedInternalSelectList: Arr::get($data, 'AssignedInternalSelectList'),
            selectListsAssigned: Arr::get($data, 'SelectListsAssigned'),
            selectListInfos: Arr::get($data, 'SelectListInfos'),
            calculateSum: Arr::get($data, 'CalculateSum'),
            links: LinkData::collection(Arr::get($data, 'Links')),
        );
    }
}
