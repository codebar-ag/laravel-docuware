<?php

namespace CodebarAg\DocuWare\Data\Users;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\LinkData;
use CodebarAg\DocuWare\Data\Support\Field;
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
            id: Field::string($data, 'Id', self::class),
            name: (string) Arr::get($data, 'Name', ''),
            salutation: Arr::get($data, 'Salutation'),
            firstName: Arr::get($data, 'FirstName'),
            lastName: Arr::get($data, 'LastName'),
            dbName: (string) Arr::get($data, 'DBName', ''),
            email: (string) Arr::get($data, 'EMail', ''),
            active: Field::bool($data, 'Active'),
            isHighSecurity: Field::bool($data, 'IsHighSecurity'),
            defaultWebBasket: (string) Arr::get($data, 'DefaultWebBasket', ''),
            outOfOffice: is_array($oof = Arr::get($data, 'OutOfOffice')) ? OutOfOfficeData::fromDocuWare($oof) : null,
            regionalSettings: is_array($rs = Arr::get($data, 'RegionalSettings')) ? RegionalSettingsData::fromDocuWare($rs) : null,
            links: LinkData::collection(Arr::get($data, 'Links')),
            shouldUpdateActive: Arr::get($data, 'ShouldUpdateActive'),
            twoStepVerificationEnabled: Arr::get($data, 'TwoStepVerificationEnabled'),
        );
    }
}
