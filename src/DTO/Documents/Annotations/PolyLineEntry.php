<?php

namespace CodebarAg\DocuWare\DTO\Documents\Annotations;

final class PolyLineEntry implements AnnotationEntry
{
    /**
     * @param  list<Point>  $points
     */
    public function __construct(
        public array $points,
        public string $color = 'Red',
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
            '$type' => 'PolyLineEntry',
            'Stroke' => [
                'Point' => array_map(static fn (Point $point): array => $point->toArray(), $this->points),
                '_do_not_use' => false,
            ],
            'Color' => $this->color,
            'Rotation' => $this->rotation,
            'Transparent' => $this->transparent,
            'StrokeWidth' => $this->strokeWidth,
        ];
    }
}
