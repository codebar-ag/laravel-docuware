<?php

namespace CodebarAg\DocuWare\DTO\Documents\Annotations;

final class TextEntry implements AnnotationEntry
{
    public function __construct(
        public string $value,
        public Location $location,
        public Font $font = new Font,
        public string $color = 'Black',
        public int $rotation = 0,
        public bool $transparent = false,
        public int $strokeWidth = 50,
        /** Set to update an existing text annotation (its GUID). */
        public ?string $id = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $entry = [
            '$type' => 'TextEntry',
            'Font' => $this->font->toArray(),
            'Value' => $this->value,
            'Location' => $this->location->toArray(),
            'Color' => $this->color,
            'Rotation' => $this->rotation,
            'Transparent' => $this->transparent,
            'StrokeWidth' => $this->strokeWidth,
        ];

        if ($this->id !== null) {
            // DocuWare expects the lowercase "id" key when updating an existing annotation.
            $entry['id'] = $this->id;
        }

        return $entry;
    }
}
