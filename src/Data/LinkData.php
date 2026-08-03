<?php

namespace CodebarAg\DocuWare\Data;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * A hypermedia link (`{ "rel": ..., "href": ... }`) present in the `Links` array of
 * virtually every DocuWare Platform resource.
 */
final class LinkData extends DocuWareData
{
    public function __construct(
        public string $rel,
        public string $href,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            rel: (string) Arr::get($data, 'rel', ''),
            href: (string) Arr::get($data, 'href', ''),
        );
    }

    /**
     * Map a raw `Links` array to a collection of LinkData.
     *
     * @return Collection<int, self>
     */
    public static function collection(mixed $links): Collection
    {
        return collect(is_array($links) ? $links : [])
            ->filter(fn ($link) => is_array($link))
            ->map(fn (array $link) => self::fromDocuWare($link))
            ->values();
    }
}
