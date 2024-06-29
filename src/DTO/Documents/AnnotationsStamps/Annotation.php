<?php

namespace CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class Annotation
{
    public static function fromData(array $data): self
    {
        return new self(
            items: Arr::has($data, 'Items') ? collect(Arr::get($data, 'Items'))->map(fn (array $item) => AnnotationItem::fromData($item)) : null,
            id: Arr::get($data, 'Id'),
        );
    }

    public function __construct(
        public ?Collection $items,
        public int $id,
    ) {}

    public function values(): array
    {
        return [
            'Items' => $this->items->values(),
            'Id' => $this->id,
        ];
    }
}
