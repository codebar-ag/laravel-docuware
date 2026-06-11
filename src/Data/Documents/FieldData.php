<?php

namespace CodebarAg\DocuWare\Data\Documents;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\LinkData;
use CodebarAg\DocuWare\Data\Support\Field;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class FieldData extends DocuWareData
{
    /**
     * @param  array<int|string, mixed>|null  $tableFieldColumns
     * @param  Collection<int, LinkData>|null  $links
     */
    public function __construct(
        public string $name,
        public string $label,
        public string $type,
        public string $scope,
        public ?int $length = null,
        public ?int $precision = null,
        public ?bool $notEmpty = null,
        public ?bool $usedAsDocumentName = null,
        public ?bool $dropLeadingZero = null,
        public ?bool $dropLeadingBlanks = null,
        public ?array $tableFieldColumns = null,
        public ?Collection $links = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            name: Field::string($data, 'DBFieldName', self::class),
            label: (string) Arr::get($data, 'DisplayName', ''),
            type: (string) Arr::get($data, 'DWFieldType', ''),
            scope: (string) Arr::get($data, 'Scope', ''),
            length: Arr::get($data, 'Length'),
            precision: Arr::get($data, 'Precision'),
            notEmpty: Arr::get($data, 'NotEmpty'),
            usedAsDocumentName: Arr::get($data, 'UsedAsDocumentName'),
            dropLeadingZero: Arr::get($data, 'DropLeadingZero'),
            dropLeadingBlanks: Arr::get($data, 'DropLeadingBlanks'),
            tableFieldColumns: Arr::get($data, 'TableFieldColumns'),
            links: LinkData::collection(Arr::get($data, 'Links')),
        );
    }

    public function isSystem(): bool
    {
        return $this->scope === 'System';
    }

    public function isUser(): bool
    {
        return $this->scope === 'User';
    }
}
