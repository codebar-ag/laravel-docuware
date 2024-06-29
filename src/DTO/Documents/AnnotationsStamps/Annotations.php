<?php

namespace CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class Annotations
{
    public static function fromData(array $data): self
    {
        return new self(
            pageNumber: Arr::get($data, 'PageNumber'),
            sectionNumber: Arr::get($data, 'SectionNumber'),
            annotationsPlacement: Arr::has($data, 'AnnotationsPlacement') ? AnnotationPlacement::fromData(Arr::get($data, 'AnnotationsPlacement')) : null,
            annotation: Arr::has($data, 'Annotation') ? collect(Arr::get($data, 'Annotation'))->map(fn ($item) => Annotation::fromData($item)) : null,
        );
    }

    public function __construct(
        public ?int $pageNumber,
        public ?int $sectionNumber,
        public ?AnnotationPlacement $annotationsPlacement = null,
        public ?Collection $annotation = null,
    ) {}

    public function values(): array
    {
        $values = [
            'PageNumber' => $this->pageNumber,
            'SectionNumber' => $this->sectionNumber,
            'AnnotationsPlacement' => $this->annotationsPlacement?->values(),
            'Annotation' => $this->annotation?->map(fn (Annotation $item) => $item->values())->toArray(),
        ];

        if (is_null($values['AnnotationsPlacement'])) {
            unset($values['AnnotationsPlacement']);
        }

        if (is_null($values['Annotation'])) {
            unset($values['Annotation']);
        }

        return $values;
    }
}
