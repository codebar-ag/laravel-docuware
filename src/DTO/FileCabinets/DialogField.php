<?php

namespace CodebarAg\DocuWare\DTO\FileCabinets;

use Illuminate\Support\Arr;

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
        );
    }

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
    ) {}
}
