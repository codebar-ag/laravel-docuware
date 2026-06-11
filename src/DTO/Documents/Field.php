<?php

namespace CodebarAg\DocuWare\DTO\Documents;

use CodebarAg\DocuWare\DTO\Link;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class Field
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        return new self(
            name: Arr::get($data, 'DBFieldName'),
            label: Arr::get($data, 'DisplayName'),
            type: Arr::get($data, 'DWFieldType'),
            scope: Arr::get($data, 'Scope'),
            length: Arr::get($data, 'Length'),
            precision: Arr::get($data, 'Precision'),
            notEmpty: Arr::get($data, 'NotEmpty'),
            usedAsDocumentName: Arr::get($data, 'UsedAsDocumentName'),
            dropLeadingZero: Arr::get($data, 'DropLeadingZero'),
            dropLeadingBlanks: Arr::get($data, 'DropLeadingBlanks'),
            tableFieldColumns: Arr::get($data, 'TableFieldColumns'),
            links: Link::collection(Arr::get($data, 'Links')),
        );
    }

    /**
     * @param  array<int|string, mixed>|null  $tableFieldColumns
     * @param  Collection<int, Link>|null  $links
     */
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $type,
        public readonly string $scope,
        public readonly ?int $length = null,
        public readonly ?int $precision = null,
        public readonly ?bool $notEmpty = null,
        public readonly ?bool $usedAsDocumentName = null,
        public readonly ?bool $dropLeadingZero = null,
        public readonly ?bool $dropLeadingBlanks = null,
        public readonly ?array $tableFieldColumns = null,
        public readonly ?Collection $links = null,
    ) {}

    public function isSystem(): bool
    {
        return $this->scope === 'System';
    }

    public function isUser(): bool
    {
        return $this->scope === 'User';
    }

    public static function fake(
        ?string $name = null,
        ?string $label = null,
        ?string $type = null,
        ?string $scope = null,
    ): self {
        return new self(
            name: $name ?? 'FAKE_FIELD',
            label: $label ?? 'Fake Field',
            type: Arr::random(['Text', 'Memo', 'Numeric', 'Decimal', 'Date', 'DateTime', 'Keyword']),
            scope: $scope ?? Arr::random(['System', 'User']),
        );
    }
}
