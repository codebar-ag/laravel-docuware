<?php

namespace CodebarAg\DocuWare\DTO\Documents;

use Illuminate\Support\Arr;

/**
 * The `ChecksumInfo` object of a DocuWare document.
 */
final class ChecksumInfo
{
    public function __construct(
        public readonly string $checksumAlgorithm,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        return new self(
            checksumAlgorithm: (string) Arr::get($data, 'ChecksumAlgorithm', 'None'),
        );
    }

    public static function fake(string $checksumAlgorithm = 'None'): self
    {
        return new self($checksumAlgorithm);
    }
}
