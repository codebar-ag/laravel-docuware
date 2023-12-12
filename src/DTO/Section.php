<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;

final class Section
{
    public static function fromJson(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            contentType: Arr::get($data, 'ContentType'),
            haveMorePages: Arr::get($data, 'HaveMorePages'),
            pageCount: Arr::get($data, 'PageCount'),
            fileSize: Arr::get($data, 'FileSize'),
            originalFileName: Arr::get($data, 'OriginalFileName'),
            contentModified: Arr::get($data, 'ContentModified'),
            annotationsPreview: Arr::get($data, 'AnnotationsPreview'),
            hasTextAnnotations: Arr::get($data, 'HasTextAnnotations'),
        );
    }

    public function __construct(
        public string $id,
        public string $contentType,
        public bool $haveMorePages,
        public int $pageCount,
        public int $fileSize,
        public string $originalFileName,
        public string $contentModified,
        public bool $annotationsPreview,
        public ?bool $hasTextAnnotations = null,
    ) {
    }
}
