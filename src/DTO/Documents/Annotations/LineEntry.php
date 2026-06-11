<?php

namespace CodebarAg\DocuWare\DTO\Documents\Annotations;

final class LineEntry implements AnnotationEntry
{
    public function __construct(
        public Point $from,
        public Point $to,
        public bool $arrow = false,
        public string $color = 'Cyan',
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
            '$type' => 'LineEntry',
            'From' => $this->from->toArray(),
            'To' => $this->to->toArray(),
            'Arrow' => $this->arrow,
            'Color' => $this->color,
            'Rotation' => $this->rotation,
            'Transparent' => $this->transparent,
            'StrokeWidth' => $this->strokeWidth,
        ];
    }
}
