<?php

namespace CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class AnnotationItem
{
    public static function fromData(array $data): self
    {
        if (Arr::has($data, 'Layer')) {
            if (is_array(Arr::get($data, 'Layer'))) {
                $layer = collect(Arr::get($data, 'Layer'))->map(fn (array $layer) => AnnotationItemLayer::fromData($layer));
            } else {
                $layer = Arr::get($data, 'Layer');
            }
        }

        return new self(
            type: Arr::get($data, '$type'),
            layer: $layer ?? null,
            field: collect(Arr::get($data, 'Field'))
                ->map(fn (array $field) => AnnotationItemField::fromData($field)),
            password: Arr::get($data, 'Password'),
            font: Arr::has($data, 'Font') ? AnnotationItemFont::fromData(Arr::get($data, 'Font')) : null,
            location: Arr::has($data, 'Location') ? AnnotationItemLocation::fromData(Arr::get($data, 'Location')) : null,
            headFont: Arr::has($data, 'HeadFont') ? AnnotationItemFont::fromData(Arr::get($data, 'HeadFont')) : null,
            signature: Arr::get($data, 'Signature'),
            userName: Arr::get($data, 'UserName'),
            visible: Arr::get($data, 'Visible'),
            frame: Arr::get($data, 'Frame'),
            showUser: Arr::get($data, 'ShowUser'),
            showDate: Arr::get($data, 'ShowDate'),
            showTime: Arr::get($data, 'ShowTime'),
            created: Arr::has($data, 'Created') ? AnnotationItemCreated::fromData(Arr::get($data, 'Created')) : null,
            color: Arr::get($data, 'Color'),
            rotation: Arr::get($data, 'Rotation'),
            transparent: Arr::get($data, 'Transparent'),
            strokeWidth: Arr::get($data, 'StrokeWidth'),
            stampId: Arr::get($data, 'StampId') ?? Arr::get($data, 'Id'),
        );
    }

    public function __construct(
        public string $type,
        public null|Collection|int $layer = null,
        public ?Collection $field = null,
        public ?string $password = null,
        public ?AnnotationItemFont $font = null,
        public ?AnnotationItemLocation $location = null,
        public ?AnnotationItemFont $headFont = null,
        public ?string $signature = null,
        public ?string $userName = null,
        public ?bool $visible = null,
        public ?bool $frame = null,
        public ?bool $showUser = null,
        public ?bool $showDate = null,
        public ?bool $showTime = null,
        public ?AnnotationItemCreated $created = null,
        public ?string $color = null,
        public ?int $rotation = null,
        public ?bool $transparent = null,
        public ?int $strokeWidth = null,
        public ?string $stampId = null,
    ) {}

    public function values(): array
    {
        if ($this->layer instanceof Collection) {
            $layer = $this->layer->map(fn (AnnotationItemLayer $layer) => $layer->values())->toArray();
        } else {
            $layer = $this->layer;
        }

        $values = [
            '$type' => $this->type,
            'Layer' => $layer,
            'Field' => $this->field?->map(fn (AnnotationItemField $field) => $field->values())->toArray(),
            'Password' => $this->password,
            'Font' => $this->font,
            'Location' => $this->location?->values(),
            'HeadFont' => $this->headFont,
            'StampId' => $this->stampId,
        ];

        if (is_null($values['Field'])) {
            unset($values['Field']);
        }

        if (is_null($values['Password'])) {
            unset($values['Password']);
        }

        if (is_null($values['Font'])) {
            unset($values['Font']);
        }

        if (is_null($values['Location'])) {
            unset($values['Location']);
        }

        if (is_null($values['HeadFont'])) {
            unset($values['HeadFont']);
        }

        if (is_null($values['StampId'])) {
            unset($values['StampId']);
        }

        if (is_null($values['Layer'])) {
            unset($values['Layer']);
        }

        return $values;
    }
}
