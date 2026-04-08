<?php

namespace CodebarAg\DocuWare\DTO\Documents;

use Carbon\Carbon;
use CodebarAg\DocuWare\DTO\Section;
use CodebarAg\DocuWare\DTO\SuggestionField;
use CodebarAg\DocuWare\Support\JsonArrays;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class Document
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        $fields = Arr::has($data, 'Fields')
            ? self::convertFields(collect(JsonArrays::listOfRecords(Arr::get($data, 'Fields'))))
            : null;

        $sections = Arr::has($data, 'Sections')
            ? self::convertSections(collect(JsonArrays::listOfRecords(Arr::get($data, 'Sections'))))
            : null;

        $suggestions = Arr::has($data, 'Suggestions')
            ? self::convertSuggestions(collect(JsonArrays::listOfRecords(Arr::get($data, 'Suggestions'))))
            : null;

        return new self(
            id: Arr::get($data, 'Id'),
            file_size: Arr::get($data, 'FileSize'),
            total_pages: Arr::get($data, 'TotalPages'),
            title: Arr::get($data, 'Title'),
            extension: self::extensionFromFields($fields),
            content_type: Arr::get($data, 'ContentType'),
            file_cabinet_id: Arr::get($data, 'FileCabinetId'),
            intellixTrust: Arr::get($data, 'IntellixTrust'),
            created_at: ParseValue::date(Arr::get($data, 'CreatedAt')),
            updated_at: ParseValue::date(Arr::get($data, 'LastModified')),
            fields: $fields,
            sections: $sections,
            suggestions: $suggestions,
        );
    }

    /**
     * @param  Collection<string, DocumentField>|null  $fields
     */
    protected static function extensionFromFields(?Collection $fields): ?string
    {
        if ($fields === null) {
            return null;
        }

        $field = $fields->get('DWEXTENSION');
        if (! $field instanceof DocumentField) {
            return null;
        }

        $value = $field->value;

        return is_string($value) ? $value : null;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $fields
     * @return Collection<string, DocumentField>
     */
    protected static function convertFields(Collection $fields): Collection
    {
        return $fields
            ->filter(fn (array $field) => is_string(Arr::get($field, 'FieldName')) && Arr::get($field, 'FieldName') !== '')
            ->mapWithKeys(fn (array $field) => [Arr::get($field, 'FieldName') => DocumentField::fromJson($field)]);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $suggestions
     * @return Collection<string, SuggestionField>
     */
    protected static function convertSuggestions(Collection $suggestions): Collection
    {
        return $suggestions
            ->filter(fn (array $suggestion) => is_string(Arr::get($suggestion, 'DBName')) && Arr::get($suggestion, 'DBName') !== '')
            ->mapWithKeys(fn (array $suggestion) => [Arr::get($suggestion, 'DBName') => SuggestionField::fromJson($suggestion)]);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $sections
     * @return Collection<int, Section>
     */
    protected static function convertSections(Collection $sections): Collection
    {
        return $sections
            ->filter(fn (array $section) => filled(Arr::get($section, 'Id')))
            ->mapWithKeys(fn (array $section) => [Arr::get($section, 'Id') => Section::fromJson($section)]);
    }

    /**
     * @param  Collection<string, DocumentField>|null  $fields
     * @param  Collection<int, Section>|null  $sections
     * @param  Collection<string, SuggestionField>|null  $suggestions
     */
    public function __construct(
        public int $id,
        public int $file_size,
        public int $total_pages,
        public string $title,
        public ?string $extension,
        public string $content_type,
        public string $file_cabinet_id,
        public ?string $intellixTrust,
        public Carbon $created_at,
        public Carbon $updated_at,
        public ?Collection $fields,
        public ?Collection $sections,
        public ?Collection $suggestions,
    ) {}

    public function isPdf(): bool
    {
        return $this->content_type === 'application/pdf';
    }

    public function isWord(): bool
    {
        return in_array($this->content_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    public function isExcel(): bool
    {
        return $this->content_type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    public function isImage(): bool
    {
        return in_array($this->content_type, [
            'image/png',
            'image/svg+xml',
        ]);
    }

    public function isBinary(): bool
    {
        return $this->content_type === 'application/octet-stream';
    }

    public function fileName(): string
    {
        $name = Str::snake($this->title);

        return "{$name}{$this->extension}";
    }

    /**
     * @param  Collection<string, DocumentField>|null  $fields
     * @param  Collection<int, Section>|null  $sections
     * @param  Collection<string, SuggestionField>|null  $suggestions
     */
    public static function fake(
        ?int $id = null,
        ?int $file_size = null,
        ?int $total_pages = null,
        ?string $title = null,
        ?string $extension = null,
        ?string $content_type = null,
        ?string $file_cabinet_id = null,
        ?string $intellixTrust = null,
        ?Carbon $created_at = null,
        ?Carbon $updated_at = null,
        ?Collection $fields = null,
        ?Collection $sections = null,
        ?Collection $suggestions = null,
    ): self {
        return new self(
            id: $id ?? random_int(1, 999999),
            file_size: $file_size ?? random_int(1000, 999999),
            total_pages: $total_pages ?? random_int(1, 100),
            title: $title ?? 'Fake Title',
            extension: $extension ?? '.pdf',
            content_type: $content_type ?? 'application/pdf',
            file_cabinet_id: $file_cabinet_id ?? (string) Str::uuid(),
            intellixTrust: $intellixTrust ?? 'Red',
            created_at: $created_at ?? now()->subDay(),
            updated_at: $updated_at ?? now(),
            fields: $fields ?? collect([
                DocumentField::fake(),
                DocumentField::fake(),
            ]),
            sections: $sections ?? null,
            suggestions: $suggestions ?? null
        );
    }
}
