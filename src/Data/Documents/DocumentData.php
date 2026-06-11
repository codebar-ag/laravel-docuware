<?php

namespace CodebarAg\DocuWare\Data\Documents;

use Carbon\Carbon;
use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\LinkData;
use CodebarAg\DocuWare\Data\SectionData;
use CodebarAg\DocuWare\Data\SuggestionFieldData;
use CodebarAg\DocuWare\Data\Support\Field;
use CodebarAg\DocuWare\Support\JsonArrays;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * A DocuWare document with its index fields, sections, suggestions and metadata.
 */
final class DocumentData extends DocuWareData
{
    /**
     * @param  Collection<string, DocumentFieldData>|null  $fields
     * @param  Collection<int, SectionData>|null  $sections
     * @param  Collection<string, SuggestionFieldData>|null  $suggestions
     * @param  Collection<int, LinkData>|null  $links
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
        public ?Carbon $created_at,
        public ?Carbon $updated_at,
        public ?Collection $fields,
        public ?Collection $sections,
        public ?Collection $suggestions,
        public ?bool $annotations_preview = null,
        public ?ChecksumInfoData $checksum_info = null,
        public ?DocumentFlagsData $flags = null,
        public ?bool $has_text_annotation = null,
        public ?bool $has_xml_digital_signatures = null,
        public ?bool $have_more_total_pages = null,
        public ?Collection $links = null,
        public ?string $organization_guid = null,
        public ?int $section_count = null,
        public ?DocumentVersionData $version = null,
        public ?string $version_status = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        $fields = Arr::has($data, 'Fields')
            ? collect(JsonArrays::listOfRecords(Arr::get($data, 'Fields')))
                ->filter(fn (array $field) => is_string(Arr::get($field, 'FieldName')) && Arr::get($field, 'FieldName') !== '')
                ->mapWithKeys(fn (array $field) => [Arr::get($field, 'FieldName') => DocumentFieldData::fromDocuWare($field)])
            : null;

        $sections = Arr::has($data, 'Sections')
            ? collect(JsonArrays::listOfRecords(Arr::get($data, 'Sections')))
                ->filter(fn (array $section) => filled(Arr::get($section, 'Id')))
                ->mapWithKeys(fn (array $section) => [Arr::get($section, 'Id') => SectionData::fromDocuWare($section)])
            : null;

        $suggestions = Arr::has($data, 'Suggestions')
            ? collect(JsonArrays::listOfRecords(Arr::get($data, 'Suggestions')))
                ->filter(fn (array $s) => is_string(Arr::get($s, 'DBName')) && Arr::get($s, 'DBName') !== '')
                ->mapWithKeys(fn (array $s) => [Arr::get($s, 'DBName') => SuggestionFieldData::fromDocuWare($s)])
            : null;

        return new self(
            id: Field::int($data, 'Id', self::class),
            file_size: (int) Arr::get($data, 'FileSize', 0),
            total_pages: (int) Arr::get($data, 'TotalPages', 0),
            title: (string) Arr::get($data, 'Title', ''),
            extension: self::extensionFromFields($fields),
            content_type: (string) Arr::get($data, 'ContentType', ''),
            file_cabinet_id: (string) Arr::get($data, 'FileCabinetId', ''),
            intellixTrust: Arr::get($data, 'IntellixTrust'),
            created_at: ParseValue::dateOrNull(Field::stringOrNull($data, 'CreatedAt')),
            updated_at: ParseValue::dateOrNull(Field::stringOrNull($data, 'LastModified')),
            fields: $fields,
            sections: $sections,
            suggestions: $suggestions,
            annotations_preview: Arr::get($data, 'AnnotationsPreview'),
            checksum_info: is_array($checksum = Arr::get($data, 'ChecksumInfo')) ? ChecksumInfoData::fromDocuWare($checksum) : null,
            flags: is_array($flags = Arr::get($data, 'Flags')) ? DocumentFlagsData::fromDocuWare($flags) : null,
            has_text_annotation: Arr::get($data, 'HasTextAnnotation'),
            has_xml_digital_signatures: Arr::get($data, 'HasXmlDigitalSignatures'),
            have_more_total_pages: Arr::get($data, 'HaveMoreTotalPages'),
            links: LinkData::collection(Arr::get($data, 'Links')),
            organization_guid: Arr::get($data, 'OrganizationGuid'),
            section_count: Arr::get($data, 'SectionCount'),
            version: is_array($version = Arr::get($data, 'Version')) ? DocumentVersionData::fromDocuWare($version) : null,
            version_status: Arr::get($data, 'VersionStatus'),
        );
    }

    /**
     * @param  Collection<string, DocumentFieldData>|null  $fields
     */
    private static function extensionFromFields(?Collection $fields): ?string
    {
        $field = $fields?->get('DWEXTENSION');

        return $field instanceof DocumentFieldData && is_string($field->value) ? $field->value : null;
    }

    public function isPdf(): bool
    {
        return $this->content_type === 'application/pdf';
    }

    public function isWord(): bool
    {
        return in_array($this->content_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ], true);
    }

    public function isExcel(): bool
    {
        return $this->content_type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    public function isImage(): bool
    {
        return in_array($this->content_type, ['image/png', 'image/svg+xml'], true);
    }

    public function isBinary(): bool
    {
        return $this->content_type === 'application/octet-stream';
    }

    public function fileName(): string
    {
        return Str::snake($this->title).$this->extension;
    }
}
