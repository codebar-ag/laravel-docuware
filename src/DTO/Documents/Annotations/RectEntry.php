<?php

namespace CodebarAg\DocuWare\DTO\Documents\Annotations;

final class RectEntry implements AnnotationEntry
{
    public function __construct(
        public Location $location,
        public bool $filled = true,
        public bool $ellipse = false,
        public string $color = 'Bisque',
        public int $rotation = 0,
        public bool $transparent = false,
        public int $strokeWidth = 3,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            '$type' => 'RectEntry',
            'Location' => $this->location->toArray(),
            'Filled' => $this->filled,
            'Ellipse' => $this->ellipse,
            'Color' => $this->color,
            'Rotation' => $this->rotation,
            'Transparent' => $this->transparent,
            'StrokeWidth' => $this->strokeWidth,
        ];
    }
}
