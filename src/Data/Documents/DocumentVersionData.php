<?php

namespace CodebarAg\DocuWare\Data\Documents;

use CodebarAg\DocuWare\Data\DocuWareData;
use Illuminate\Support\Arr;

/**
 * The `Version` object of a DocuWare document (`{ "Major": ..., "Minor": ... }`).
 */
final class DocumentVersionData extends DocuWareData
{
    public function __construct(
        public int $major,
        public int $minor,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            major: (int) Arr::get($data, 'Major', 0),
            minor: (int) Arr::get($data, 'Minor', 0),
        );
    }
}
