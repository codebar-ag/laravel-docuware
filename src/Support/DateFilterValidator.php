<?php

namespace CodebarAg\DocuWare\Support;

use Carbon\Carbon;
use CodebarAg\DocuWare\DocuWareSearchRequestBuilder;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;

/**
 * Collects and validates the date bounds of a single search query.
 *
 * DocuWare expresses a date filter as one or two bounds per field. This class
 * snaps each bound to the correct edge of the day, keeps at most two bounds per
 * field, rejects diverging ranges, and fills a half-open range with a synthetic
 * opposite bound — the date-specific logic extracted out of
 * {@see DocuWareSearchRequestBuilder}.
 *
 * @internal
 */
class DateFilterValidator
{
    /** @var array<string, array<int, Carbon>> */
    private array $filters = [];

    /** @var array<string, list<string>> */
    private array $usedDateOperators = [];

    public function add(string $name, string $operator, Carbon $date): void
    {
        $date = $this->exactDateTime($date, $operator);

        $insertAt = $this->reserveSlot($name, $operator);

        if ($insertAt === null) {
            $this->filters[$name][] = $date;
        } else {
            $this->filters[$name] ??= [];
            array_splice($this->filters[$name], $insertAt, 0, [$date]);
            array_splice($this->usedDateOperators[$name], $insertAt, 0, [$operator]);
        }

        $this->filters[$name] = array_values($this->filters[$name]);
    }

    /**
     * Validate the collected ranges and return the finalized date conditions keyed by field.
     *
     * @return array<string, array<int, Carbon>>
     */
    public function conditions(): array
    {
        $this->checkRangeDivergence();
        $this->fillMonoRange();

        return $this->filters;
    }

    private function checkRangeDivergence(): void
    {
        foreach ($this->usedDateOperators as $name => $operators) {
            if (count($operators) !== 2) {
                continue;
            }

            throw_if(
                $this->dateBoundsViolateRange($this->filters[$name][0], $this->filters[$name][1], $operators[0]),
                UnableToSearch::DivergedDateFilterRange(),
            );
        }
    }

    /**
     * First operator between the two stored bounds (same semantics as the previous eval-based check).
     */
    private function dateBoundsViolateRange(Carbon $left, Carbon $right, string $operator): bool
    {
        $a = $left->getTimestamp();
        $b = $right->getTimestamp();

        return match ($operator) {
            '>=' => $a >= $b,
            '>' => $a > $b,
            '<=' => $a <= $b,
            '<' => $a < $b,
            default => false,
        };
    }

    private function fillMonoRange(): void
    {
        foreach ($this->usedDateOperators as $name => $operators) {
            if (count($operators) !== 1) {
                continue;
            }

            $this->filters[$name][] = $this->syntheticOppositeDateBound($operators[0]);
            $this->filters[$name] = array_values($this->filters[$name]);
        }
    }

    private function syntheticOppositeDateBound(string $operator): Carbon
    {
        return match ($operator) {
            '<=', '<' => Carbon::createFromTimestamp(0),
            '>=', '>' => Carbon::now(),
            default => Carbon::now(),
        };
    }

    /**
     * @return int|null insert index when replacing an existing operator-bound date; null to append (operator already registered for this call)
     */
    private function reserveSlot(string $name, string $operator): ?int
    {
        if (! isset($this->usedDateOperators[$name])) {
            $this->usedDateOperators[$name][] = $operator;
            $this->throwIfInvalidDateFiltersCount($name);

            return null;
        }

        $operatorIndex = array_search($operator, $this->usedDateOperators[$name], true);
        if ($operatorIndex !== false) {
            unset($this->filters[$name][$operatorIndex]);
            $this->filters[$name] = isset($this->filters[$name])
                ? array_values($this->filters[$name])
                : [];
            array_splice($this->usedDateOperators[$name], $operatorIndex, 1);
            $this->throwIfInvalidDateFiltersCount($name);

            return $operatorIndex;
        }

        if ($operator === '=') {
            unset($this->filters[$name]);
            $this->usedDateOperators[$name] = [$operator];
            $this->throwIfInvalidDateFiltersCount($name);

            return null;
        }

        $this->usedDateOperators[$name][] = $operator;
        $this->throwIfInvalidDateFiltersCount($name);

        return null;
    }

    /**
     * DocuWare allows at most one open range per field before {@see fillMonoRange()} fills the pair.
     */
    private function throwIfInvalidDateFiltersCount(string $name): void
    {
        if (! isset($this->filters[$name])) {
            return;
        }

        if (count($this->filters[$name]) !== 2) {
            return;
        }

        throw UnableToSearch::InvalidDateFiltersCount(2);
    }

    private function exactDateTime(Carbon $date, string $operator): Carbon
    {
        return match ($operator) {
            '<', '>=' => $date->startOfDay(),
            '>', '<=' => $date->endOfDay(),
            default => $date,
        };
    }
}
