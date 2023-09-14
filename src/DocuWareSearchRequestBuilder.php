<?php

namespace CodebarAg\DocuWare;

use Carbon\Carbon;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;
use CodebarAg\DocuWare\Requests\Search\GetSearchRequest;
use Illuminate\Support\Str;
use Saloon\Exceptions\InvalidResponseClassException;
use Saloon\Exceptions\PendingRequestException;

class DocuWareSearchRequestBuilder
{
    protected ?string $fileCabinetId = null;

    protected ?string $dialogId = null;

    protected array $additionalFileCabinetIds = [];

    protected int $page = 1;

    protected int $perPage = 50;

    protected ?string $searchTerm = null;

    protected string $orderField = 'DWSTOREDATETIME';

    protected string $orderDirection = 'asc';

    protected array $filters = [];

    protected array $usedDateOperators = [];

    public function fileCabinet(string $fileCabinetId): self
    {
        $this->fileCabinetId = $fileCabinetId;

        return $this;
    }

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
        if (is_null($page)) {
            $this->page = 1;
        } else {
            $this->page = $page;
        }

        return $this;
    }

    public function perPage(?int $perPage): self
    {
        if (is_null($perPage)) {
            $this->perPage = 50;
        } else {
            $this->perPage = $perPage;
        }

        return $this;
    }

    public function fulltext(?string $searchTerm): self
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    public function filterDate(string $name, string $operator, ?Carbon $date): self
    {
        $date = $this->exactDateTime($date, $operator);

        $this->makeSureFilterDateRangeIsCorrect($name, $operator);

        $this->filters[$name][] = $date;
        $this->filters[$name] = array_values($this->filters[$name]);

        return $this;
    }

    public function orderBy(string $field, ?string $direction = 'asc'): self
    {
        $this->orderField = $field;

        if (is_null($direction)) {
            $this->orderDirection = 'asc';
        } else {
            $this->orderDirection = $direction; // Supported values: 'asc', 'desc'
        }

        return $this;
    }

    public function filter(string $name, mixed $value): self
    {
        if (is_string($value)) {
            $value = "\"{$value}\"";
        }

        $this->filters[$name][] = $value;

        return $this;
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidResponseClassException
     * @throws PendingRequestException
     */
    public function get(): GetSearchRequest
    {
        $this->checkDateFilterRangeDivergence();
        $this->restructureMonoDateFilterRange();
        $this->guard();

        $condition = [];

        if (Str::length($this->searchTerm) >= 1) {
            $condition[] = [
                'DBName' => 'DocuWareFulltext',
                'Value' => [$this->searchTerm],
            ];
        }

        foreach ($this->filters as $name => $value) {
            if (empty($value)) {
                continue;
            }

            $condition[] = [
                'DBName' => $name,
                'Value' => $value,
            ];
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

    protected function guard(): void
    {
        throw_if(
            is_null($this->fileCabinetId),
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

    private function checkDateFilterRangeDivergence(): void
    {
        foreach ($this->usedDateOperators as $name => $operators) {
            if (count($operators) == 2) {
                foreach ($operators as $index => $operator) {
                    throw_if(
                        eval("return {$this->filters[$name][$index]->timestamp} {$operator} {$this->filters[$name][$index + 1]->timestamp};"),
                        UnableToSearch::DivergedDateFilterRange(),
                    );
                    break;
                }
            }
        }
    }

    private function restructureMonoDateFilterRange(): void
    {
        foreach ($this->usedDateOperators as $name => $operators) {
            if (count($operators) == 1) {
                $this->filters[$name][] = match ($operators[0]) {
                    '<=', '<' => Carbon::createFromTimestamp(0),
                    '>=', '>' => now(),
                    default => now(),
                };
            }
        }
    }

    private function makeSureFilterDateRangeIsCorrect($name, $operator): void
    {
        if (isset($this->usedDateOperators[$name])) {
            if ($operatorFilterIndex = array_search($operator, $this->usedDateOperators[$name])) {
                unset($this->filters[$name][$operatorFilterIndex]);
            } elseif ($operator == '=') {
                unset($this->filters[$name]);
                $this->usedDateOperators[$name] = [$operator];
            } else {
                $this->usedDateOperators[$name][] = $operator;
            }
        } else {
            $this->usedDateOperators[$name][] = $operator;
        }

        if (isset($this->filters[$name]) && ($dateFiltersCount = count($this->filters[$name])) == 2) {
            throw UnableToSearch::InvalidDateFiltersCount($dateFiltersCount);
        }
    }

    private function exactDateTime($date, $operator): Carbon
    {
        return match ($operator) {
            '<', '>=' => $date->startOfDay(),
            '>', '<=' => $date->endOfDay(),
            default => $date,
        };
    }
}
