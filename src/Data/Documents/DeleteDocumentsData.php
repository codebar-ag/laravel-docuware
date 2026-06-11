<?php

namespace CodebarAg\DocuWare\Data\Documents;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;

/**
 * Result of a trash-bin delete operation: the failed items and the count of successes.
 */
final class DeleteDocumentsData extends DocuWareData
{
    /**
     * @param  list<array<string, mixed>>  $failedItems
     */
    public function __construct(
        public array $failedItems = [],
        public int $successCount = 0,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        $failedRaw = Arr::get($data, 'FailedItems', []);
        $failedItems = JsonArrays::listOfRecords(is_array($failedRaw) ? $failedRaw : []);
        $successCount = Arr::get($data, 'SuccessCount');

        return new self(
            failedItems: $failedItems,
            successCount: $successCount,
        );
    }
}
