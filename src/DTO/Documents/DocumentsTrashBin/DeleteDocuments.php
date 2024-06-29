<?php

namespace CodebarAg\DocuWare\DTO\Documents\DocumentsTrashBin;

use Illuminate\Support\Arr;

final class DeleteDocuments
{
    public static function fromData(array $data): self
    {
        $failedItems = Arr::get($data, 'FailedItems');
        $successCount = Arr::get($data, 'SuccessCount');

        return new self(
            failedItems: $failedItems,
            successCount: $successCount,
        );
    }

    public function __construct(
        public array $failedItems = [],
        public int $successCount = 0,
    ) {}
}
