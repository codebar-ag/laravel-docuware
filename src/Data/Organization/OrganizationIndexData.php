<?php

namespace CodebarAg\DocuWare\Data\Organization;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\LinkData;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class OrganizationIndexData extends DocuWareData
{
    /**
     * @param  Collection<int, LinkData>|null  $links
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $guid = null,
        public ?Collection $links = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            name: Arr::get($data, 'Name'),
            guid: Arr::get($data, 'Guid'),
            links: LinkData::collection(Arr::get($data, 'Links')),
        );
    }
}
