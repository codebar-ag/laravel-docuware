<?php

namespace CodebarAg\DocuWare\Data\Documents;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * One page of trash-bin documents. The trash endpoint returns column `Headers` + `Rows`; each
 * row is mapped to a field-name => value map in {@see self::$documents}.
 */
final class TrashPageData extends DocuWareData
{
    /**
     * @param  Collection<int, Collection<string, mixed>>  $documents
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

        $headers = self::headerMap(Arr::get($data, 'Headers', []));

        $rowsRaw = Arr::get($data, 'Rows', []);
        $documents = collect(JsonArrays::listOfRecords(is_array($rowsRaw) ? $rowsRaw : []))
            ->map(fn (array $row) => self::mapRow($row, $headers));

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

    /**
     * @return Collection<string, array<string, mixed>>
     */
    private static function headerMap(mixed $raw): Collection
    {
        $map = [];
        if (is_array($raw)) {
            foreach ($raw as $key => $value) {
                if (is_array($value)) {
                    $map[(string) $key] = JsonArrays::associativeRow($value);
                }
            }
        }

        return collect($map);
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  Collection<string, array<string, mixed>>  $headers
     * @return Collection<string, mixed>
     */
    private static function mapRow(array $row, Collection $headers): Collection
    {
        return collect(JsonArrays::associativeRow($row))
            ->mapWithKeys(function (mixed $value, int|string $key) use ($headers): array {
                $header = collect($headers->get((string) $key, []));
                $fieldName = $header->get('FieldName');

                return $header->has('FieldName') && (is_string($fieldName) || is_int($fieldName))
                    ? [(string) $fieldName => $value]
                    : [];
            })
            ->filter();
    }
}
