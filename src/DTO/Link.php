<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * A hypermedia link (`{ "rel": ..., "href": ... }`) as returned in the `Links`
 * array of virtually every DocuWare Platform resource.
 */
final class Link
{
    public function __construct(
        public readonly string $rel,
        public readonly string $href,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        return new self(
            rel: (string) Arr::get($data, 'rel', ''),
            href: (string) Arr::get($data, 'href', ''),
        );
    }

    /**
     * Map a raw `Links` array to a collection of Link DTOs.
     *
     * @param  mixed  $links
     * @return Collection<int, self>
     */
    public static function collection($links): Collection
    {
        return collect(is_array($links) ? $links : [])
            ->filter(fn ($link) => is_array($link))
            ->map(fn (array $link) => self::fromJson($link))
            ->values();
    }

    public static function fake(?string $rel = null, ?string $href = null): self
    {
        return new self(
            rel: $rel ?? 'self',
            href: $href ?? '/DocuWare/Platform/FileCabinets/00000000-0000-0000-0000-000000000000',
        );
    }
}
