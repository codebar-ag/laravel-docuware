<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class Textshot
{
    public static function fromJson(array $data): self
    {
        $pages = collect(Arr::get($data, 'Pages', []));

        return new self(
            page_count: $pages->count(),
            pages: TextshotPage::fromCollection($pages),
        );
    }

    public function __construct(
        public int $page_count,
        public Collection $pages,
    ) {}
}
