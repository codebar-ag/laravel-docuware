<?php

namespace CodebarAg\DocuWare\Data\Organization;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\LinkData;
use CodebarAg\DocuWare\Data\Support\Field;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class OrganizationData extends DocuWareData
{
    /**
     * @param  array<string, mixed>  $additionalInfo
     * @param  array<string, mixed>  $configurationRights
     * @param  Collection<int, LinkData>|null  $links
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $guid = null,
        public array $additionalInfo = [],
        public array $configurationRights = [],
        public ?Collection $links = null,
        public ?bool $isTwoStepVerificationEnabled = null,
        public ?bool $isTwoStepVerificationRequired = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            id: Field::string($data, 'Id', self::class),
            name: (string) Arr::get($data, 'Name', ''),
            guid: Arr::get($data, 'Guid'),
            additionalInfo: Arr::get($data, 'AdditionalInfo', []),
            configurationRights: Arr::get($data, 'ConfigurationRights', []),
            links: LinkData::collection(Arr::get($data, 'Links')),
            isTwoStepVerificationEnabled: Arr::get($data, 'IsTwoStepVerificationEnabled'),
            isTwoStepVerificationRequired: Arr::get($data, 'IsTwoStepVerificationRequired'),
        );
    }
}
