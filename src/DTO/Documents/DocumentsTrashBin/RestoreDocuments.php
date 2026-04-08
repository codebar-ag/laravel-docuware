<?php

namespace CodebarAg\DocuWare\DTO\Documents\DocumentsTrashBin;

use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;

final class RestoreDocuments
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromData(array $data): self
    {
        $failedRaw = Arr::get($data, 'FailedItems', []);
        $failedItems = JsonArrays::listOfRecords(is_array($failedRaw) ? $failedRaw : []);
        $successCount = Arr::get($data, 'SuccessCount');

        return new self(
            failedItems: $failedItems,
            successCount: $successCount,
        );
    }

    /**
     * @param  list<array<string, mixed>>  $failedItems
     */
    public function __construct(
        public array $failedItems = [],
        public int $successCount = 0,
    ) {}
}
