<?php

namespace CodebarAg\DocuWare\DTO\General\Organization;

use CodebarAg\DocuWare\DTO\Link;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class Organization
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            name: Arr::get($data, 'Name'),
            guid: Arr::get($data, 'Guid'),
            additionalInfo: Arr::get($data, 'AdditionalInfo', []),
            configurationRights: Arr::get($data, 'ConfigurationRights', []),
            links: Link::collection(Arr::get($data, 'Links')),
            isTwoStepVerificationEnabled: Arr::get($data, 'IsTwoStepVerificationEnabled'),
            isTwoStepVerificationRequired: Arr::get($data, 'IsTwoStepVerificationRequired'),
        );
    }

    /**
     * @param  array<string, mixed>  $additionalInfo
     * @param  array<string, mixed>  $configurationRights
     * @param  Collection<int, Link>|null  $links
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $guid = null,
        public readonly array $additionalInfo = [],
        public readonly array $configurationRights = [],
        public readonly ?Collection $links = null,
        public readonly ?bool $isTwoStepVerificationEnabled = null,
        public readonly ?bool $isTwoStepVerificationRequired = null,
    ) {}

    /**
     * @param  array<string, mixed>  $additionalInfo
     * @param  array<string, mixed>  $configurationRights
     * @param  Collection<int, Link>|null  $links
     */
    public static function fake(
        ?string $id = null,
        ?string $name = null,
        ?string $guid = null,
        array $additionalInfo = [],
        array $configurationRights = [],
        ?Collection $links = null,
        ?bool $isTwoStepVerificationEnabled = null,
        ?bool $isTwoStepVerificationRequired = null,
    ): self {
        return new self(
            id: $id ?? (string) Str::uuid(),
            name: $name ?? 'Fake File Cabinet',
            guid: $guid ?? (string) Str::uuid(),
            additionalInfo: $additionalInfo,
            configurationRights: $configurationRights,
            links: $links ?? collect([Link::fake()]),
            isTwoStepVerificationEnabled: $isTwoStepVerificationEnabled ?? false,
            isTwoStepVerificationRequired: $isTwoStepVerificationRequired ?? false,
        );
    }
}
