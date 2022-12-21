<?php

namespace CodebarAg\DocuWare\DTO;

use Carbon\Carbon;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DocumentField
{
    public static function fromJson(array $data): self
    {
        return new self(
            systemField: $data['SystemField'],
            name: $data['FieldName'],
            label: $data['FieldLabel'],
            isNull: $data['IsNull'],
            value: ParseValue::field($data),
            type: $data['ItemElementName'],
        );
    }

    public function __construct(
        public bool $systemField,
        public string $name,
        public string $label,
        public bool $isNull,
        public null|int|float|Carbon|string|Collection $value,
        public string $type,
    ) {
    }

    public static function fake(
        ?bool $systemField = false,
        ?string $name = null,
        ?string $label = null,
        ?bool $isNull = true,
        null|int|float|Carbon|string $value = null,
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
            systemField: $systemField ?? false,
            name: $name ?? 'FAKE_DOCUMENT_FIELD',
            label: $label ?? 'Fake Document Field',
            isNull: $isNull ?? true,
            value: $value ?? $fakeValue,
            type: $type ?? $fakeType,
        );
    }
}
