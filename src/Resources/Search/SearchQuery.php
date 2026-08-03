<?php

namespace CodebarAg\DocuWare\Resources\Search;

use Carbon\Carbon;
use CodebarAg\DocuWare\Client\DocuWareClient;
use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Data\Documents\DocumentPageData;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;
use CodebarAg\DocuWare\Requests\Search\GetSearchRequest;
use CodebarAg\DocuWare\Support\DateFilterValidator;
use CodebarAg\DocuWare\Transport\ResponseValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

/**
 * Fluent, immutable-ish search over a file cabinet. Terminal methods execute against the
 * instance connector and return {@see DocumentData} / {@see DocumentPageData}, or a memory-safe
 * lazy {@see LazyCollection} via {@see self::cursor()}.
 */
final class SearchQuery
{
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

    public function __construct(
        protected readonly DocuWareClient $client,
        protected ?string $fileCabinetId = null,
    ) {
        $this->dateFilters = new DateFilterValidator;
    }

    /**
     * @param  list<string>  $fileCabinetIds
     */
    public function fileCabinets(array $fileCabinetIds): self
    {
        $this->fileCabinetId = $fileCabinetIds[0] ?? $this->fileCabinetId;
        $this->additionalFileCabinetIds = array_slice($fileCabinetIds, 1);

        return $this;
    }

    public function dialog(string $dialogId): self
    {
        $this->dialogId = $dialogId;

        return $this;
    }

    public function page(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function fullText(?string $searchTerm): self
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    public function where(string $name, mixed $value): self
    {
        $this->filters[$name][] = $this->prepareValue($value);

        return $this;
    }

    public function whereIn(string $name, mixed $values): self
    {
        if (is_string($values)) {
            return $this->where($name, $values);
        }

        $list = match (true) {
            is_array($values) => array_values($values),
            $values instanceof Collection => $values->values()->all(),
            default => [],
        };

        $prepared = collect($list)->map(fn (mixed $value) => $this->prepareValue($value))->all();
        $this->filters[$name][] = implode(' OR ', $prepared);

        return $this;
    }

    public function whereEmpty(string $name): self
    {
        $this->filters[$name][] = 'EMPTY()';

        return $this;
    }

    public function whereNotEmpty(string $name): self
    {
        $this->filters[$name][] = 'NOTEMPTY()';

        return $this;
    }

    public function whereDate(string $name, string $operator, Carbon $date): self
    {
        $this->dateFilters->add($name, $operator, $date);

        return $this;
    }

    public function whereDateBetween(string $name, Carbon $from, Carbon $to): self
    {
        $this->dateFilters->add($name, '>=', $from);
        $this->dateFilters->add($name, '<=', $to);

        return $this;
    }

    public function orderBy(string $field, string $direction = 'asc'): self
    {
        $this->orderField = $field;
        $this->orderDirection = $direction;

        return $this;
    }

    public function latest(string $field = 'DWSTOREDATETIME'): self
    {
        return $this->orderBy($field, 'desc');
    }

    public function oldest(string $field = 'DWSTOREDATETIME'): self
    {
        return $this->orderBy($field, 'asc');
    }

    /**
     * Fetch the currently-configured page.
     */
    public function get(): DocumentPageData
    {
        return $this->fetchPage($this->page);
    }

    /**
     * The first matching document, or null.
     */
    public function first(): ?DocumentData
    {
        $clone = clone $this;

        return $clone->perPage(1)->fetchPage(1)->documents->first();
    }

    /**
     * Total number of matching documents (filtered), without downloading a full page.
     */
    public function count(): int
    {
        $clone = clone $this;

        return $clone->perPage(1)->fetchPage(1)->total;
    }

    /**
     * Lazily iterate every matching document across all pages — memory-safe.
     *
     * @return LazyCollection<int, DocumentData>
     */
    public function cursor(): LazyCollection
    {
        return LazyCollection::make(function () {
            $page = 1;

            do {
                $result = $this->fetchPage($page);

                // Yield with auto-incrementing keys (not `yield from`, which would repeat the
                // per-page Collection keys 0,1,… and collide when keys are preserved downstream).
                foreach ($result->documents as $document) {
                    yield $document;
                }

                $page++;
            } while ($page <= $result->lastPage);
        });
    }

    private function fetchPage(int $page): DocumentPageData
    {
        $this->guard();

        $request = new GetSearchRequest(
            fileCabinetId: $this->fileCabinetId,
            dialogId: $this->dialogId,
            additionalFileCabinetIds: $this->additionalFileCabinetIds,
            page: $page,
            perPage: $this->perPage,
            searchTerm: $this->searchTerm,
            orderField: $this->orderField,
            orderDirection: $this->orderDirection,
            condition: $this->buildCondition(),
        );

        $response = $this->client->connector()->send($request);

        ResponseValidator::validate($response, $this->client->name());

        return DocumentPageData::fromDocuWare($response->json(), $page, $this->perPage);
    }

    private function prepareValue(mixed $value): mixed
    {
        return is_string($value) ? "\"{$value}\"" : $value;
    }

    /**
     * @return list<array{DBName: string, Value: list<mixed>}>
     */
    private function buildCondition(): array
    {
        $condition = [];

        if (Str::length($this->searchTerm) >= 1) {
            $condition[] = ['DBName' => 'DocuWareFulltext', 'Value' => [$this->searchTerm]];
        }

        foreach ([...$this->filters, ...$this->dateFilters->conditions()] as $name => $value) {
            if ($value === []) {
                continue;
            }

            $condition[] = ['DBName' => $name, 'Value' => array_values($value)];
        }

        return $condition;
    }

    private function guard(): void
    {
        throw_if(is_null($this->fileCabinetId), UnableToSearch::cabinetNotSet());
        throw_if($this->page <= 0, UnableToSearch::invalidPageNumber($this->page));
        throw_if($this->perPage <= 0, UnableToSearch::invalidPerPageNumber($this->perPage));
    }
}
