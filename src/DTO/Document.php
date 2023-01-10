<?php

namespace CodebarAg\DocuWare\DTO;

use Carbon\Carbon;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class Document
{
    public static function fromJson(array $data): self
    {
        $fields = self::convertFields(collect($data['Fields']));

        return new self(
            id: $data['Id'],
            file_size: $data['FileSize'],
            total_pages: $data['TotalPages'],
            title: $data['Title'],
            extension: $fields['DWEXTENSION']->value,
            content_type: $data['ContentType'],
            file_cabinet_id: $data['FileCabinetId'],
            created_at: ParseValue::date($data['CreatedAt']),
            updated_at: ParseValue::date($data['LastModified']),
            fields: $fields,
        );
    }

    protected static function convertFields(Collection $fields): Collection
    {
        return $fields->mapWithKeys(function (array $field) {
            return [$field['FieldName'] => DocumentField::fromJson($field)];
        });
    }

    public function __construct(
        public int $id,
        public int $file_size,
        public int $total_pages,
        public string $title,
        public string|null $extension,
        public string $content_type,
        public string $file_cabinet_id,
        public Carbon $created_at,
        public Carbon $updated_at,
        public Collection $fields,
    ) {
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

    public static function fake(
        ?int $id = null,
        ?int $file_size = null,
        ?int $total_pages = null,
        ?string $title = null,
        ?string $extension = null,
        ?string $content_type = null,
        ?string $file_cabinet_id = null,
        ?Carbon $created_at = null,
        ?Carbon $updated_at = null,
        ?Collection $fields = null,
    ): self {
        return new static(
            id: $id ?? random_int(1, 999999),
            file_size: $file_size ?? random_int(1000, 999999),
            total_pages: $total_pages ?? random_int(1, 100),
            title: $title ?? 'Fake Title',
            extension: $extension ?? '.pdf',
            content_type: $content_type ?? 'application/pdf',
            file_cabinet_id: $file_cabinet_id ?? (string) Str::uuid(),
            created_at: $created_at ?? now()->subDay(),
            updated_at: $updated_at ?? now(),
            fields: $fields ?? collect([
                DocumentField::fake(),
                DocumentField::fake(),
            ]),
        );
    }
}
