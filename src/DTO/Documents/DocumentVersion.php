<?php

namespace CodebarAg\DocuWare\DTO\Documents;

use Illuminate\Support\Arr;

/**
 * The `Version` object of a DocuWare document (`{ "Major": ..., "Minor": ... }`).
 */
final class DocumentVersion
{
    public function __construct(
        public readonly int $major,
        public readonly int $minor,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        return new self(
            major: (int) Arr::get($data, 'Major', 0),
            minor: (int) Arr::get($data, 'Minor', 0),
        );
    }

    public static function fake(int $major = 1, int $minor = 0): self
    {
        return new self($major, $minor);
    }
}
