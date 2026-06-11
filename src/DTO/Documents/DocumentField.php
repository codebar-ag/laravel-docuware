<?php

namespace CodebarAg\DocuWare\DTO\Documents;

use Carbon\Carbon;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class DocumentField
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        return new self(
            systemField: Arr::get($data, 'SystemField'),
            name: Arr::get($data, 'FieldName'),
            label: Arr::get($data, 'FieldLabel'),
            isNull: Arr::get($data, 'IsNull'),
            value: ParseValue::field($data),
            type: Arr::get($data, 'ItemElementName'),
            readOnly: Arr::get($data, 'ReadOnly'),
        );
    }

    /**
     * @param  null|int|float|Carbon|string|Collection<int, mixed>  $value
     */
    public function __construct(
        public readonly bool $systemField,
        public readonly string $name,
        public readonly string $label,
        public readonly bool $isNull,
        public readonly null|int|float|Carbon|string|Collection $value,
        public readonly string $type,
        public readonly ?bool $readOnly = null,
    ) {}

    public static function fake(
        ?bool $systemField = false,
        ?string $name = null,
        ?string $label = null,
        ?bool $isNull = true,
        int|float|Carbon|string|null $value = null,
        ?string $type = null,
        ?bool $readOnly = null,
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
            readOnly: $readOnly ?? false,
        );
    }
}
