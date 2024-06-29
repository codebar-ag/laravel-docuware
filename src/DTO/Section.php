<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class Section
{
    public static function fromJson(array $data): self
    {
        if ($contentModifiedDateTime = Arr::get($data, 'ContentModified')) {
            $contentModifiedDateTime = Str::of($contentModifiedDateTime)->after('(')->before(')');
            $contentModifiedDateTime = Carbon::createFromTimestamp($contentModifiedDateTime);
        }

        return new self(
            id: Arr::get($data, 'Id'),
            contentType: Arr::get($data, 'ContentType'),
            haveMorePages: Arr::get($data, 'HaveMorePages'),
            pageCount: Arr::get($data, 'PageCount'),
            fileSize: Arr::get($data, 'FileSize'),
            originalFileName: Arr::get($data, 'OriginalFileName'),
            contentModified: $contentModifiedDateTime,
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
        public ?Carbon $contentModified,
        public bool $annotationsPreview,
        public ?bool $hasTextAnnotations = null,
    ) {
    }
}
