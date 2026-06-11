<?php

namespace CodebarAg\DocuWare\Data\Users;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\LinkData;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * A DocuWare user account.
 *
 * Properties are mutable (non-readonly) to support the read-modify-write flow used when
 * toggling a user's active state; {@see DocuWareData::copyWith()} provides an immutable wither.
 */
final class UserData extends DocuWareData
{
    /**
     * @param  Collection<int, LinkData>|null  $links
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $salutation,
        public ?string $firstName,
        public ?string $lastName,
        public string $dbName,
        public string $email,
        public bool $active,
        public bool $isHighSecurity,
        public string $defaultWebBasket,
        public ?OutOfOfficeData $outOfOffice,
        public ?RegionalSettingsData $regionalSettings,
        public ?Collection $links = null,
        public ?bool $shouldUpdateActive = null,
        public ?bool $twoStepVerificationEnabled = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            name: Arr::get($data, 'Name'),
            salutation: Arr::get($data, 'Salutation'),
            firstName: Arr::get($data, 'FirstName'),
            lastName: Arr::get($data, 'LastName'),
            dbName: Arr::get($data, 'DBName'),
            email: Arr::get($data, 'EMail'),
            active: Arr::get($data, 'Active'),
            isHighSecurity: Arr::get($data, 'IsHighSecurity'),
            defaultWebBasket: Arr::get($data, 'DefaultWebBasket'),
            outOfOffice: Arr::has($data, 'OutOfOffice') ? OutOfOfficeData::fromDocuWare(Arr::get($data, 'OutOfOffice')) : null,
            regionalSettings: Arr::has($data, 'RegionalSettings') ? RegionalSettingsData::fromDocuWare(Arr::get($data, 'RegionalSettings')) : null,
            links: LinkData::collection(Arr::get($data, 'Links')),
            shouldUpdateActive: Arr::get($data, 'ShouldUpdateActive'),
            twoStepVerificationEnabled: Arr::get($data, 'TwoStepVerificationEnabled'),
        );
    }
}
