<?php

namespace CodebarAg\DocuWare\DTO\FileCabinets;

use CodebarAg\DocuWare\DTO\Link;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * A single field exposed by a dialog. Shape follows the DocuWare "Get a Specific Dialog"
 * response (DBFieldName / DlgLabel / DWFieldType / …).
 */
final class DialogField
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
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
            links: Link::collection(Arr::get($data, 'Links')),
        );
    }

    /**
     * @param  array<int|string, mixed>|null  $selectListsAssigned
     * @param  array<int|string, mixed>|null  $selectListInfos
     * @param  Collection<int, Link>|null  $links
     */
    public function __construct(
        public readonly string $dbName,
        public readonly string $label,
        public readonly string $type,
        public readonly int $length,
        public readonly int $precision,
        public readonly bool $locked,
        public readonly bool $readOnly,
        public readonly bool $notEmpty,
        public readonly bool $visible,
        public readonly bool $allowExtendedSearch,
        public readonly bool $usedAsDocumentName,
        public readonly bool $isHierarchy,
        public readonly ?bool $allowFiltering = null,
        public readonly ?bool $selectListOnly = null,
        public readonly ?string $selectListType = null,
        public readonly ?string $assignedInternalSelectList = null,
        public readonly ?array $selectListsAssigned = null,
        public readonly ?array $selectListInfos = null,
        public readonly ?bool $calculateSum = null,
        public readonly ?Collection $links = null,
    ) {}
}
