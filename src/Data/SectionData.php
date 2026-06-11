<?php

namespace CodebarAg\DocuWare\Data;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * A document section (one stored file within a document).
 *
 * Note: `ContentModified` is a `/Date(seconds)/` value here (seconds, not milliseconds),
 * preserved exactly from the validated v1 mapping.
 */
final class SectionData extends DocuWareData
{
    /**
     * @param  Collection<int, LinkData>|null  $links
     * @param  array<string, mixed>|null  $pages
     * @param  list<string>|null  $signatureStatus
     * @param  array<string, mixed>|null  $thumbnails
     */
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
        public ?Collection $links = null,
        public ?array $pages = null,
        public ?array $signatureStatus = null,
        public ?array $thumbnails = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        $contentModified = Arr::get($data, 'ContentModified');
        if ($contentModified) {
            $timestamp = Str::of($contentModified)->after('(')->before(')')->__toString();
            $contentModified = Carbon::createFromTimestamp($timestamp);
        } else {
            $contentModified = null;
        }

        return new self(
            id: (string) Arr::get($data, 'Id'),
            contentType: (string) Arr::get($data, 'ContentType'),
            haveMorePages: (bool) Arr::get($data, 'HaveMorePages'),
            pageCount: (int) Arr::get($data, 'PageCount'),
            fileSize: (int) Arr::get($data, 'FileSize'),
            originalFileName: (string) Arr::get($data, 'OriginalFileName'),
            contentModified: $contentModified,
            annotationsPreview: (bool) Arr::get($data, 'AnnotationsPreview'),
            hasTextAnnotations: Arr::get($data, 'HasTextAnnotation'),
            links: LinkData::collection(Arr::get($data, 'Links')),
            pages: Arr::get($data, 'Pages'),
            signatureStatus: Arr::get($data, 'SignatureStatus'),
            thumbnails: Arr::get($data, 'Thumbnails'),
        );
    }
}
