<?php

namespace CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps;

use Illuminate\Support\Arr;

final class AnnotationItemLocation
{
    public static function fromData(array $data): self
    {
        return new self(
            x: Arr::get($data, 'X'),
            y: Arr::get($data, 'Y'),

        );
    }

    public function __construct(
        public ?string $x = null,
        public ?string $y = null,
        public ?string $left = null,
        public ?string $top = null,
        public ?string $width = null,
        public ?string $height = null,
    ) {
    }

    public function values(): array
    {
        return [
            'X' => $this->x,
            'Y' => $this->y,
            'Left' => $this->left,
            'Top' => $this->top,
            'Width' => $this->width,
            'Height' => $this->height,
        ];
    }
}
