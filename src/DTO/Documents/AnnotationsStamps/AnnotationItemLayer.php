<?php

namespace CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class AnnotationItemLayer
{
    public static function fromData(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            items: collect(Arr::get($data, 'Items'))->map(fn (array $layer) => AnnotationItemLayerItem::fromData($layer)),
        );
    }

    public function __construct(
        public string $id,
        public Collection $items,
    ) {
    }

    public function values(): array
    {
        return [
            'Id' => $this->id,
            'Items' => $this->items->map(fn (AnnotationItemLayerItem $item) => $item->values())->toArray(),
        ];
    }
}
