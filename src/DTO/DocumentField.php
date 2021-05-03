<?php

namespace CodebarAg\DocuWare\DTO;

use Carbon\Carbon;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Arr;

class DocumentField
{
    public static function fromJson(array $data): self
    {
        return new self(
            name: $data['FieldName'],
            label: $data['FieldLabel'],
            value: ParseValue::field($data),
            type: $data['ItemElementName'],
        );
    }

    public function __construct(
        public string $name,
        public string $label,
        public null | int | float | Carbon | string $value,
        public string $type,
    ) {
    }

    public static function fake(
        ?string $name = null,
        ?string $label = null,
        null | int | float | Carbon | string $value = null,
        ?string $type = null,
    ): self {
        $fakeType = Arr::random(['Int', 'Decimal', 'Text', 'DateTime']);

        $fakeValue = match ($fakeType) {
            'Int' => random_int(1, 9999),
            'Decimal' => mt_rand() / mt_getrandmax(),
            'DateTime' => now(),
            default => 'FakeText',
        };

        return new self(
            name: $name ?? 'FAKE_DOCUMENT_FIELD',
            label: $label ?? 'Fake Document Field',
            value: $value ?? $fakeValue,
            type: $type ?? $fakeType,
        );
    }
}
