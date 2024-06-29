<?php

namespace CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps;

use Illuminate\Support\Arr;

final class AnnotationItemField
{
    public static function fromData(array $data): self
    {
        return new self(
            name: Arr::get($data, 'Name'),
            typedValue: Arr::get($data, 'TypedValue'),
            value: Arr::get($data, 'Value'),
            textAsString: Arr::get($data, 'TextAsString'),
        );
    }

    public function __construct(
        public string $name,
        public AnnotationItemFieldTypedValue $typedValue,
        public string $value,
        public string $textAsString,
    ) {}

    public function values(): array
    {
        return [
            'Name' => $this->name,
            'TypedValue' => $this->typedValue->values(),
            'Value' => $this->value,
            'TextAsString' => $this->textAsString,
        ];
    }
}
