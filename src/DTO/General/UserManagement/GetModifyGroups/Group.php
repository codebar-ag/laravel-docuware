<?php

namespace CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyGroups;

use CodebarAg\DocuWare\DTO\Link;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class Group
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            name: Arr::get($data, 'Name'),
            active: Arr::get($data, 'Active'),
            links: Link::collection(Arr::get($data, 'Links')),
        );
    }

    /**
     * @param  Collection<int, Link>|null  $links
     */
    public function __construct(
        public string $id,
        public string $name,
        public bool $active,
        public ?Collection $links = null,
    ) {}
}
