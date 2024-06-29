<?php

namespace CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class AnnotationPlacement
{
    public static function fromData(array $data): self
    {
        return new self(
            items: collect(Arr::get($data, 'Items'))
                ->map(fn (array $item) => AnnotationItem::fromData($item)),
        );
    }

    public function __construct(
        public Collection $items,
    ) {
    }

    public function values(): array
    {
        return [
            'Items' => $this->items->map(fn (AnnotationItem|AnnotationItemLayerItem $item) => $item->values())->toArray(),
        ];
    }
}
