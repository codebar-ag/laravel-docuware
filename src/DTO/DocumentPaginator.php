<?php

namespace codebar\DocuWare\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DocumentPaginator
{
    public static function fromJson(
        array $data,
        int $page,
        int $perPage,
    ): self {
        $total = Arr::get($data, 'Count.Value');

        $lastPage = (int) ceil($total / $perPage);

        $from = $page === 1 ? 1 : (($page - 1) * $perPage) + 1;

        $to = $page === $lastPage ? $total : $page * $perPage;

        $items = collect($data['Items'])->map(function (array $document) {
            return Document::fromJson($document);
        });

        return new self(
            total: $total,
            per_page: $perPage,
            current_page: $page,
            last_page: $lastPage,
            from: $from,
            to: $to,
            items: $items,
        );
    }

    public function __construct(
        public int $total,
        public int $per_page,
        public int $current_page,
        public int $last_page,
        public int $from,
        public int $to,
        public Collection $items,
    ) {
    }

    public function showPrev(): bool
    {
        return $this->current_page > 1;
    }

    public function showNext(): bool
    {
        return $this->current_page < $this->last_page;
    }
}
