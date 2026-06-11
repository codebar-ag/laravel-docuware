<?php

namespace CodebarAg\DocuWare\Data\Documents;

use CodebarAg\DocuWare\Data\DocuWareData;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * One page of a document search/listing. Operations return this or throw — there is no
 * `ErrorBag`/`fromFailed` hybrid in 2.0.
 */
final class DocumentPageData extends DocuWareData
{
    /**
     * @param  Collection<int, DocumentData>  $documents
     */
    public function __construct(
        public int $total,
        public int $perPage,
        public int $currentPage,
        public int $lastPage,
        public int $from,
        public int $to,
        public Collection $documents,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data, int $page, int $perPage): self
    {
        $total = (int) Arr::get($data, 'Count.Value', 0);
        $lastPage = $total > 0 ? (int) ceil($total / $perPage) : 0;
        $from = $page === 1 ? 1 : (($page - 1) * $perPage) + 1;
        $to = $page === $lastPage ? $total : $page * $perPage;

        $documents = collect(is_array($items = Arr::get($data, 'Items', [])) ? $items : [])
            ->filter(fn ($item) => is_array($item))
            ->map(fn (array $item) => DocumentData::fromDocuWare($item))
            ->values();

        return new self(
            total: $total,
            perPage: $perPage,
            currentPage: $page,
            lastPage: $lastPage,
            from: $from,
            to: $to,
            documents: $documents,
        );
    }

    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    public function onFirstPage(): bool
    {
        return $this->currentPage <= 1;
    }
}
