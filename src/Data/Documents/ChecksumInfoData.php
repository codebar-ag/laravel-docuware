<?php

namespace CodebarAg\DocuWare\Data\Documents;

use CodebarAg\DocuWare\Data\DocuWareData;
use Illuminate\Support\Arr;

/**
 * The `ChecksumInfo` object of a DocuWare document.
 */
final class ChecksumInfoData extends DocuWareData
{
    public function __construct(
        public string $checksumAlgorithm,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            checksumAlgorithm: (string) Arr::get($data, 'ChecksumAlgorithm', 'None'),
        );
    }
}
