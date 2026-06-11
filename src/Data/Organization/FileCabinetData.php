<?php

namespace CodebarAg\DocuWare\Data\Organization;

use CodebarAg\DocuWare\Data\DocuWareData;
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
            color: Arr::get($data, 'Color'),
            name: Arr::get($data, 'Name'),
            id: Arr::get($data, 'Id'),
            isBasket: Arr::get($data, 'IsBasket'),
            usable: Arr::get($data, 'Usable'),
            default: Arr::get($data, 'Default'),
            assignedCabinetId: Arr::get($data, 'AssignedCabinetId'),
            versionManagement: Arr::get($data, 'VersionManagement'),
            windowsExplorerClientAccess: Arr::get($data, 'WindowsExplorerClientAccess'),
            addIndexEntriesInUpperCase: Arr::get($data, 'AddIndexEntriesInUpperCase'),
            documentAuditingEnabled: Arr::get($data, 'DocumentAuditingEnabled'),
            hasFullTextSupport: Arr::get($data, 'HasFullTextSupport'),
        );
    }
}
