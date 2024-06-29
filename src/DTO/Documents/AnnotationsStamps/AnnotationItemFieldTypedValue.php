<?php

namespace CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps;

use Illuminate\Support\Arr;

final class AnnotationItemFieldTypedValue
{
    public static function fromData(array $data): self
    {
        return new self(
            item: Arr::get($data, 'Item'),
            itemElementName: Arr::get($data, 'ItemElementName'),
        );
    }

    public function __construct(
        public string $item,
        public string $itemElementName,
    ) {}

    public function values(): array
    {
        return [
            'Item' => $this->item,
            'ItemElementName' => $this->itemElementName,
        ];
    }
}
