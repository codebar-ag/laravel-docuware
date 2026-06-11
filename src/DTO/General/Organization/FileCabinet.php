<?php

namespace CodebarAg\DocuWare\DTO\General\Organization;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class FileCabinet
{
    /**
     * @param  array<string, mixed>  $data
     */
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
        public readonly string $color,
        public readonly string $name,
        public readonly string $id,
        public readonly bool $isBasket,
        public readonly bool $usable,
        public readonly bool $default,
        public readonly ?string $assignedCabinetId,
        public readonly string $versionManagement,
        public readonly bool $windowsExplorerClientAccess,
        public readonly bool $addIndexEntriesInUpperCase,
        public readonly bool $documentAuditingEnabled,
        public readonly bool $hasFullTextSupport,
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
