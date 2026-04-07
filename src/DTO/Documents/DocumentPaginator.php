<?php

namespace CodebarAg\DocuWare\DTO\Documents;

use CodebarAg\DocuWare\DTO\ErrorBag;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, Document> $documents
 */
class DocumentPaginator
{
    /**
     * @param  Collection<int, Document>  $documents
     */
    public function __construct(
        public int $total,
        public int $per_page,
        public int $current_page,
        public int $last_page,
        public int $from,
        public int $to,
        public Collection $documents,
        public ?ErrorBag $error = null,
    ) {}

    public function showPrev(): bool
    {
        return $this->current_page > 1;
    }

    public function showNext(): bool
    {
        return $this->current_page < $this->last_page;
    }

    public function successful(): bool
    {
        return is_null($this->error);
    }

    public function failed(): bool
    {
        return ! $this->successful();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(
        array $data,
        int $page,
        int $perPage,
    ): self {
        $total = Arr::get($data, 'Count.Value');

        $lastPage = (int) ceil($total / $perPage);

        $from = $page === 1 ? 1 : (($page - 1) * $perPage) + 1;

        $to = $page === $lastPage ? $total : $page * $perPage;

        $itemsRaw = Arr::get($data, 'Items', []);
        $itemList = [];
        if (is_array($itemsRaw)) {
            foreach (array_values($itemsRaw) as $item) {
                if (is_array($item)) {
                    $itemList[] = $item;
                }
            }
        }

        $documents = collect($itemList)->map(function (array $document) {
            return Document::fromJson($document);
        });

        return new self(
            total: $total,
            per_page: $perPage,
            current_page: $page,
            last_page: $lastPage,
            from: $from,
            to: $to,
            documents: $documents,
        );
    }

    public static function fromFailed(Exception $e): self
    {
        return new self(
            total: 0,
            per_page: 0,
            current_page: 0,
            last_page: 0,
            from: 0,
            to: 0,
            documents: new Collection,
            error: ErrorBag::make($e),
        );
    }

    /**
     * @param  Collection<int, Document>|null  $documents
     */
    public static function fake(
        ?int $total = null,
        ?int $per_page = null,
        ?int $current_page = null,
        ?int $last_page = null,
        ?int $from = null,
        ?int $to = null,
        ?Collection $documents = null,
    ): self {
        return new self(
            total: $total ?? random_int(10, 100),
            per_page: $per_page ?? 10,
            current_page: $current_page ?? random_int(1, 10),
            last_page: $last_page ?? random_int(10, 20),
            from: $from ?? 1,
            to: $to ?? 10,
            documents: $documents ?? collect(range(1, 10))->map(fn () => Document::fake()),
        );
    }
}
