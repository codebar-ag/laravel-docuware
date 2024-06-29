<?php

namespace CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps;

use Illuminate\Support\Arr;

final class AnnotationItemLayerItem
{
    public static function fromData(array $data): self
    {
        return new self(
            type: Arr::get($data, '$type'),
            color: Arr::get($data, 'Color'),
            rotation: Arr::get($data, 'Rotation'),
            transparent: Arr::get($data, 'Transparent'),
            strokeWidth: Arr::get($data, 'StrokeWidth'),
            value: Arr::get($data, 'Value'),
            filled: Arr::get($data, 'Filled'),
            ellipse: Arr::get($data, 'Ellipse'),
            arrow: Arr::get($data, 'Arrow'),
            font: Arr::has($data, 'Font') ? AnnotationItemFont::fromData(Arr::get($data, 'Font')) : null,
            location: Arr::has($data, 'Location') ? AnnotationItemLocation::fromData(Arr::get($data, 'Location')) : null,
            from: Arr::has($data, 'From') ? AnnotationItemLocation::fromData(Arr::get($data, 'From')) : null,
            to: Arr::has($data, 'To') ? AnnotationItemLocation::fromData(Arr::get($data, 'To')) : null,
            id: Arr::get($data, 'Id'),
        );
    }

    public function __construct(
        public string $type,
        public string $color,
        public int $rotation,
        public bool $transparent,
        public int $strokeWidth,
        public ?string $value = null,
        public ?bool $filled = null,
        public ?bool $ellipse = null,
        public ?bool $arrow = null,
        public ?AnnotationItemFont $font = null,
        public ?AnnotationItemLocation $location = null,
        public ?AnnotationItemLocation $from = null,
        public ?AnnotationItemLocation $to = null,
        public ?int $id = null,
    ) {}

    public function values(): array
    {
        $values = [
            '$type' => $this->type,
            'Font' => $this->font?->values(),
            'Value' => $this->value,
            'Location' => $this->location?->values(),
            'Color' => $this->color,
            'Rotation' => $this->rotation,
            'Transparent' => $this->transparent,
            'StrokeWidth' => $this->strokeWidth,
            'Filled' => $this->filled,
            'Ellipse' => $this->ellipse,
            'From' => $this->from?->values(),
            'To' => $this->to?->values(),
            'Arrow' => $this->arrow,
            'Id' => $this->id,
        ];

        if (is_null($values['Location'])) {
            unset($values['Location']);
        }

        if (is_null($values['Font'])) {
            unset($values['Font']);
        }

        if (is_null($values['Value'])) {
            unset($values['Value']);
        }

        if (is_null($values['Filled'])) {
            unset($values['Filled']);
        }

        if (is_null($values['Ellipse'])) {
            unset($values['Ellipse']);
        }

        if (is_null($values['From'])) {
            unset($values['From']);
        }

        if (is_null($values['To'])) {
            unset($values['To']);
        }

        if (is_null($values['Arrow'])) {
            unset($values['Arrow']);
        }

        if (is_null($values['Id'])) {
            unset($values['Id']);
        }

        return $values;
    }
}
