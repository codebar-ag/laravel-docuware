<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class Section
{
    /**
     * @param  array<string, mixed>  $data
     */
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
            hasTextAnnotations: Arr::get($data, 'HasTextAnnotation'),
            links: Link::collection(Arr::get($data, 'Links')),
            pages: Arr::get($data, 'Pages'),
            signatureStatus: Arr::get($data, 'SignatureStatus'),
            thumbnails: Arr::get($data, 'Thumbnails'),
        );
    }

    /**
     * @param  Collection<int, Link>|null  $links
     * @param  array<string, mixed>|null  $pages
     * @param  list<string>|null  $signatureStatus
     * @param  array<string, mixed>|null  $thumbnails
     */
    public function __construct(
        public readonly string $id,
        public readonly string $contentType,
        public readonly bool $haveMorePages,
        public readonly int $pageCount,
        public readonly int $fileSize,
        public readonly string $originalFileName,
        public readonly ?Carbon $contentModified,
        public readonly bool $annotationsPreview,
        public readonly ?bool $hasTextAnnotations = null,
        public readonly ?Collection $links = null,
        public readonly ?array $pages = null,
        public readonly ?array $signatureStatus = null,
        public readonly ?array $thumbnails = null,
    ) {}
}
