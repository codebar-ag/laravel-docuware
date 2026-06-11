<?php

namespace CodebarAg\DocuWare\Data\FileCabinets;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\LinkData;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class FileCabinetInformationData extends DocuWareData
{
    /**
     * @param  Collection<int, LinkData>|null  $links
     * @param  array<int|string, mixed>|null  $fields
     * @param  array<int|string, mixed>|null  $rights
     * @param  array<int|string, mixed>|null  $extendedUserRights
     */
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
        public ?Collection $links = null,
        public ?array $fields = null,
        public ?array $rights = null,
        public ?array $extendedUserRights = null,
        public ?string $versionHistoryResultListId = null,
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
            links: LinkData::collection(Arr::get($data, 'Links')),
            fields: Arr::get($data, 'Fields'),
            rights: Arr::get($data, 'Rights'),
            extendedUserRights: Arr::get($data, 'ExtendedUserRights'),
            versionHistoryResultListId: Arr::get($data, 'VersionHistoryResultListId'),
        );
    }
}
