<?php

namespace CodebarAg\DocuWare\Data\Users;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\LinkData;
use CodebarAg\DocuWare\Data\Support\Field;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * A DocuWare user group.
 */
final class GroupData extends DocuWareData
{
    /**
     * @param  Collection<int, LinkData>|null  $links
     */
    public function __construct(
        public string $id,
        public string $name,
        public bool $active,
        public ?Collection $links = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            id: Field::string($data, 'Id', self::class),
            name: (string) Arr::get($data, 'Name', ''),
            active: Field::bool($data, 'Active'),
            links: LinkData::collection(Arr::get($data, 'Links')),
        );
    }
}
