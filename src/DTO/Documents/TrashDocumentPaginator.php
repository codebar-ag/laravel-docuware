<?php

namespace CodebarAg\DocuWare\DTO\Documents;

use CodebarAg\DocuWare\DTO\ErrorBag;
use CodebarAg\DocuWare\Support\JsonArrays;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, array<string, mixed>> $documents
 */
class TrashDocumentPaginator
{
    /**
     * @param  Collection<string, array<string, mixed>>  $headers
     * @param  Collection<int, array<string, mixed>>  $documents
     * @param  Collection<int, Collection<string, mixed>>  $mappedDocuments
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
                    $headerMap[(string) $key] = JsonArrays::associativeRow($value);
                }
            }
        }
        $headers = collect($headerMap);

        $rowsRaw = Arr::get($data, 'Rows', []);
        $documents = collect(JsonArrays::listOfRecords(is_array($rowsRaw) ? $rowsRaw : []));

        $mappedList = [];
        foreach ($documents as $document) {
            $mappedList[] = self::mapDocumentRowToFields($document, $headers);
        }
        $mappedDocuments = collect($mappedList);

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

    /**
     * @param  array<string, mixed>  $document
     * @param  Collection<string, array<string, mixed>>  $headers
     * @return Collection<string, mixed>
     */
    protected static function mapDocumentRowToFields(array $document, Collection $headers): Collection
    {
        $row = collect(JsonArrays::associativeRow($document));

        return $row->mapWithKeys(function (mixed $value, int|string $key) use ($headers): array {
            $headerRaw = $headers->get((string) $key);
            $header = collect(is_array($headerRaw) ? JsonArrays::associativeRow($headerRaw) : []);

            $fieldName = $header->get('FieldName');

            return $header->has('FieldName') && (is_string($fieldName) || is_int($fieldName))
                ? [(string) $fieldName => $value]
                : [];
        })->filter();
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
     * @param  Collection<string, array<string, mixed>>|null  $headers
     * @param  Collection<int, array<string, mixed>>|null  $documents
     * @param  Collection<int, Collection<string, mixed>>|null  $mappedDocuments
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
            headers: $headers ?? collect(),
            documents: $documents ?? collect(),
            mappedDocuments: $mappedDocuments ?? collect(),
        );
    }
}
