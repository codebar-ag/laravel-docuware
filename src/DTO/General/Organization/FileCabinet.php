<?php

namespace CodebarAg\DocuWare\DTO\General\Organization;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class FileCabinet
{
    public static function fromJson(array $data): self
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

    public static function fake(
        ?string $color = null,
        ?string $name = null,
        ?string $id = null,
        ?bool $isBasket = null,
        ?bool $usable = null,
        ?bool $default = null,
        ?string $assignedCabinetId = null,
        ?string $versionManagement = null,
        ?bool $windowsExplorerClientAccess = null,
        ?bool $addIndexEntriesInUpperCase = null,
        ?bool $documentAuditingEnabled = null,
        ?bool $hasFullTextSupport = null,
    ): self {
        return new self(
            color: $color ?? Arr::random(['Red', 'Blue', 'Black', 'Green', 'Yellow']),
            name: $name ?? 'Fake File Cabinet',
            id: $id ?? (string) Str::uuid(),
            isBasket: $isBasket ?? Arr::random([true, false]),
            usable: $usable ?? Arr::random([true, false]),
            default: $default ?? Arr::random([true, false]),
            assignedCabinetId: $assignedCabinetId ?? Arr::random([Str::uuid(), null]),
            versionManagement: $versionManagement ?? Arr::random(['Disable']),
            windowsExplorerClientAccess: $windowsExplorerClientAccess ?? Arr::random([true, false]),
            addIndexEntriesInUpperCase: $addIndexEntriesInUpperCase ?? Arr::random([true, false]),
            documentAuditingEnabled: $documentAuditingEnabled ?? Arr::random([true, false]),
            hasFullTextSupport: $hasFullTextSupport ?? Arr::random([true, false]),
        );
    }
}
