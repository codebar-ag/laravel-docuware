<?php

namespace CodebarAg\DocuWare\DTO\Documents;

use CodebarAg\DocuWare\DTO\ErrorBag;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, array<mixed>> $documents
 */
class TrashDocumentPaginator
{
    /**
     * @param  Collection<int|string, array<mixed>>  $headers
     * @param  Collection<int, array<mixed>>  $documents
     * @param  Collection<int, Collection<int|string, mixed>>  $mappedDocuments
     */
    public function __construct(
        public int $total,
        public int $per_page,
        public int $current_page,
        public int $last_page,
        public int $from,
        public int $to,
        public Collection $headers,
        public Collection $documents,
        public Collection $mappedDocuments,
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

        $headersRaw = Arr::get($data, 'Headers', []);
        $headerMap = [];
        if (is_array($headersRaw)) {
            foreach ($headersRaw as $key => $value) {
                if (is_array($value)) {
                    $headerMap[$key] = $value;
                }
            }
        }
        $headers = collect($headerMap);

        $rowsRaw = Arr::get($data, 'Rows', []);
        $rowList = [];
        if (is_array($rowsRaw)) {
            foreach (array_values($rowsRaw) as $row) {
                if (is_array($row)) {
                    $rowList[] = $row;
                }
            }
        }
        $documents = collect($rowList);

        $mappedDocuments = $documents->map(function (array $document) use ($headers): Collection {
            $document = collect($document);

            return $document->mapWithKeys(function (mixed $value, int|string $key) use ($headers): array {
                $headerRaw = $headers->get($key);
                $header = collect(is_array($headerRaw) ? $headerRaw : []);

                return $header->has('FieldName') ? [$header->get('FieldName') => $value] : [];
            })->filter();
        });

        return new self(
            total: $total,
            per_page: $perPage,
            current_page: $page,
            last_page: $lastPage,
            from: $from,
            to: $to,
            headers: $headers,
            documents: $documents,
            mappedDocuments: $mappedDocuments,
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
            headers: collect(),
            documents: collect(),
            mappedDocuments: collect(),
            error: ErrorBag::make($e),
        );
    }

    /**
     * @param  Collection<int|string, array<mixed>>|null  $headers
     * @param  Collection<int, array<mixed>>|null  $documents
     * @param  Collection<int, Collection<int|string, mixed>>|null  $mappedDocuments
     */
    public static function fake(
        ?int $total = null,
        ?int $per_page = null,
        ?int $current_page = null,
        ?int $last_page = null,
        ?int $from = null,
        ?int $to = null,
        ?Collection $headers = null,
        ?Collection $documents = null,
        ?Collection $mappedDocuments = null,
    ): self {
        return new self(
            total: $total ?? random_int(10, 100),
            per_page: $per_page ?? 10,
            current_page: $current_page ?? random_int(1, 10),
            last_page: $last_page ?? random_int(10, 20),
            from: $from ?? 1,
            to: $to ?? 10,
            headers: $headers,
            documents: $documents,
            mappedDocuments: $mappedDocuments,
        );
    }
}
