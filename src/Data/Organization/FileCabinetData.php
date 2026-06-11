<?php

namespace CodebarAg\DocuWare\Data\Organization;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\Support\Field;
use Illuminate\Support\Arr;

final class FileCabinetData extends DocuWareData
{
    public function __construct(
        public string $color,
        public string $name,
        public string $id,
        public bool $isBasket,
        public bool $usable,
        public bool $default,
        public ?string $assignedCabinetId,
        public string $versionManagement,
        public bool $windowsExplorerClientAccess,
        public bool $addIndexEntriesInUpperCase,
        public bool $documentAuditingEnabled,
        public bool $hasFullTextSupport,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            color: (string) Arr::get($data, 'Color', ''),
            name: (string) Arr::get($data, 'Name', ''),
            id: Field::string($data, 'Id', self::class),
            isBasket: Field::bool($data, 'IsBasket'),
            usable: Field::bool($data, 'Usable'),
            default: Field::bool($data, 'Default'),
            assignedCabinetId: Arr::get($data, 'AssignedCabinetId'),
            versionManagement: (string) Arr::get($data, 'VersionManagement', ''),
            windowsExplorerClientAccess: Field::bool($data, 'WindowsExplorerClientAccess'),
            addIndexEntriesInUpperCase: Field::bool($data, 'AddIndexEntriesInUpperCase'),
            documentAuditingEnabled: Field::bool($data, 'DocumentAuditingEnabled'),
            hasFullTextSupport: Field::bool($data, 'HasFullTextSupport'),
        );
    }
}
