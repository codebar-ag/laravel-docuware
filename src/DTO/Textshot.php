<?php

namespace CodebarAg\DocuWare\DTO;

use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class Textshot
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        $pages = collect(JsonArrays::listOfRecords(Arr::get($data, 'Pages', [])));

        return new self(
            page_count: $pages->count(),
            pages: TextshotPage::fromCollection($pages),
        );
    }

    /**
     * @param  Collection<int, TextshotPage>  $pages
     */
    public function __construct(
        public int $page_count,
        public Collection $pages,
    ) {}
}
