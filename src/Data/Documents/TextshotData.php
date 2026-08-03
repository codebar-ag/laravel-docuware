<?php

namespace CodebarAg\DocuWare\Data\Documents;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class TextshotData extends DocuWareData
{
    /**
     * @param  Collection<int, TextshotPageData>  $pages
     */
    public function __construct(
        public int $page_count,
        public Collection $pages,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        $pages = collect(JsonArrays::listOfRecords(Arr::get($data, 'Pages', [])));

        return new self(
            page_count: $pages->count(),
            pages: TextshotPageData::fromCollection($pages),
        );
    }
}
