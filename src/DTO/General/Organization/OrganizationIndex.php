<?php

namespace CodebarAg\DocuWare\DTO\General\Organization;

use CodebarAg\DocuWare\DTO\Link;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class OrganizationIndex
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
            links: Link::collection(Arr::get($data, 'Links')),
        );
    }

    /**
     * @param  Collection<int, Link>|null  $links
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $guid = null,
        public readonly ?Collection $links = null,
    ) {}

    public static function fake(
        ?string $id = null,
        ?string $name = null,
        ?string $guid = null,
    ): self {
        return new self(
            id: $id ?? (string) Str::uuid(),
            name: $name ?? 'Fake File Cabinet',
            guid: $guid ?? (string) Str::uuid(),
        );
    }
}
