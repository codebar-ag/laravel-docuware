<?php

namespace CodebarAg\DocuWare\Data\FileCabinets;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\LinkData;
use CodebarAg\DocuWare\Data\Support\Field;
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
            links: LinkData::collection(Arr::get($data, 'Links')),
            fields: is_array($fields = Arr::get($data, 'Fields')) ? $fields : null,
            rights: is_array($rights = Arr::get($data, 'Rights')) ? $rights : null,
            extendedUserRights: is_array($eur = Arr::get($data, 'ExtendedUserRights')) ? $eur : null,
            versionHistoryResultListId: Arr::get($data, 'VersionHistoryResultListId'),
        );
    }
}
