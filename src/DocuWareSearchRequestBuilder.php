<?php

namespace CodebarAg\DocuWare;

use Carbon\Carbon;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\GetDocuments;
use CodebarAg\DocuWare\Requests\Search\GetSearchRequest;
use CodebarAg\DocuWare\Support\DateFilterValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Saloon\Exceptions\InvalidResponseClassException;
use Saloon\Exceptions\PendingRequestException;

class DocuWareSearchRequestBuilder
{
    protected ?string $fileCabinetId = null;

    protected ?string $dialogId = null;

    /** @var list<string> */
    protected array $additionalFileCabinetIds = [];

    protected int $page = 1;

    protected int $perPage = 50;

    protected ?string $searchTerm = null;

    protected string $orderField = 'DWSTOREDATETIME';

    protected string $orderDirection = 'asc';

    /** @var array<string, array<int, mixed>> */
    protected array $filters = [];

    protected DateFilterValidator $dateFilters;

    protected bool $trashBin = false;

    public function __construct()
    {
        $this->dateFilters = new DateFilterValidator;
    }

    public function trashBin(): self
    {
        $this->trashBin = true;

        return $this;
    }

    public function fileCabinet(string $fileCabinetId): self
    {
        $this->fileCabinetId = $fileCabinetId;

        return $this;
    }

    /**
     * @param  list<string>  $fileCabinetIds
     */
    public function fileCabinets(array $fileCabinetIds): self
    {
        $this->fileCabinetId = $fileCabinetIds[0] ?? null;

        $this->additionalFileCabinetIds = array_slice($fileCabinetIds, 1);

        return $this;
    }

    public function dialog(string $dialogId): self
    {
        $this->dialogId = $dialogId;

        return $this;
    }

    public function page(?int $page): self
    {
        $this->page = $page ?? 1;

        return $this;
    }

    public function perPage(?int $perPage): self
    {
        $this->perPage = $perPage ?? 50;

        return $this;
    }

    public function fulltext(?string $searchTerm): self
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    public function filterDate(string $name, string $operator, Carbon $date): self
    {
        $this->dateFilters->add($name, $operator, $date);

        return $this;
    }

    public function orderBy(string $field, ?string $direction = 'asc'): self
    {
        $this->orderField = $field;

        $this->orderDirection = $direction ?? 'asc'; // Supported values: 'asc', 'desc'

        return $this;
    }

    private static function prepareValueForFilter(mixed $value): mixed
    {
        if (is_string($value)) {
            return "\"{$value}\"";
        }

        return $value;
    }

    public function filter(string $name, mixed $value): self
    {
        $this->filters[$name][] = self::prepareValueForFilter($value);

        return $this;
    }

    public function filterIn(string $name, mixed $values): self
    {
        if (is_string($values)) {
            return $this->filter($name, $values);
        }

        $list = match (true) {
            is_array($values) => array_values($values),
            $values instanceof Collection => $values->values()->all(),
            default => [],
        };

        $prepared = collect($list)->map(function (mixed $value) {
            return self::prepareValueForFilter($value);
        })->toArray();

        $this->filters[$name][] = implode(' OR ', $prepared);

        return $this;
    }

    /**
     * Restrict results to documents where the index field has no value (DocuWare dialog expression `EMPTY()`).
     *
     * @param  string  $name  Database field name (typically uppercase), not the display label.
     */
    public function filterEmpty(string $name): self
    {
        $this->filters[$name][] = 'EMPTY()';

        return $this;
    }

    /**
     * Restrict results to documents where the index field has a value (DocuWare dialog expression `NOTEMPTY()`).
     *
     * @param  string  $name  Database field name (typically uppercase), not the display label.
     */
    public function filterNotEmpty(string $name): self
    {
        $this->filters[$name][] = 'NOTEMPTY()';

        return $this;
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidResponseClassException
     * @throws PendingRequestException
     */
    public function get(): GetSearchRequest|GetDocuments
    {
        $dateConditions = $this->dateFilters->conditions();
        $this->guard();

        $condition = $this->buildSearchCondition($dateConditions);

        if ($this->trashBin) {
            return new GetDocuments(
                page: $this->page,
                perPage: $this->perPage,
                searchTerm: $this->searchTerm,
                orderField: $this->orderField,
                orderDirection: $this->orderDirection,
                condition: $condition,
            );
        }

        return new GetSearchRequest(
            fileCabinetId: $this->fileCabinetId,
            dialogId: $this->dialogId,
            additionalFileCabinetIds: $this->additionalFileCabinetIds,
            page: $this->page,
            perPage: $this->perPage,
            searchTerm: $this->searchTerm,
            orderField: $this->orderField,
            orderDirection: $this->orderDirection,
            condition: $condition,
        );
    }

    /**
     * @param  array<string, array<int, Carbon>>  $dateConditions
     * @return list<array{DBName: string, Value: list<mixed>}>
     */
    private function buildSearchCondition(array $dateConditions): array
    {
        $condition = [];

        if (Str::length($this->searchTerm) >= 1) {
            $condition[] = [
                'DBName' => 'DocuWareFulltext',
                'Value' => [$this->searchTerm],
            ];
        }

        foreach ([...$this->filters, ...$dateConditions] as $name => $value) {
            if ($value === []) {
                continue;
            }

            $condition[] = [
                'DBName' => $name,
                'Value' => array_values($value),
            ];
        }

        return $condition;
    }

    protected function guard(): void
    {
        throw_if(
            is_null($this->fileCabinetId) && $this->trashBin === false,
            UnableToSearch::cabinetNotSet(),
        );

        throw_if(
            $this->page <= 0,
            UnableToSearch::invalidPageNumber($this->page),
        );

        throw_if(
            $this->perPage <= 0,
            UnableToSearch::invalidPerPageNumber($this->perPage),
        );
    }
}
