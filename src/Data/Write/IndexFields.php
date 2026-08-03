<?php

namespace CodebarAg\DocuWare\Data\Write;

use Carbon\Carbon;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateTimeDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDecimalDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexKeywordDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexMemoDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexNumericDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTableDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use Illuminate\Support\Collection;

/**
 * Fluent builder for document index values used by store/update writes. Each method appends a
 * typed index entry that serializes to DocuWare's field wire format.
 *
 * ```php
 * IndexFields::make()->text('STATUS', 'open')->number('AMOUNT', 42)->date('DUE', $carbon)
 * ```
 *
 * @phpstan-type IndexEntry IndexTextDTO|IndexMemoDTO|IndexKeywordDTO|IndexNumericDTO|IndexDecimalDTO|IndexDateDTO|IndexDateTimeDTO|IndexTableDTO
 */
final class IndexFields
{
    /** @var Collection<int, IndexEntry> */
    private Collection $indexes;

    public function __construct()
    {
        $this->indexes = collect();
    }

    public static function make(): self
    {
        return new self;
    }

    public function text(string $name, ?string $value): self
    {
        return $this->push(IndexTextDTO::make($name, $value));
    }

    public function memo(string $name, ?string $value): self
    {
        return $this->push(IndexMemoDTO::make($name, $value));
    }

    /**
     * @param  string|list<string>|null  $value
     */
    public function keyword(string $name, string|array|null $value): self
    {
        $values = match (true) {
            $value === null => [],
            is_array($value) => $value,
            default => [$value],
        };

        return $this->push(IndexKeywordDTO::make($name, $values));
    }

    public function number(string $name, ?int $value): self
    {
        return $this->push(IndexNumericDTO::make($name, $value));
    }

    public function decimal(string $name, int|float|null $value): self
    {
        return $this->push(IndexDecimalDTO::make($name, $value));
    }

    public function date(string $name, ?Carbon $value): self
    {
        return $this->push(IndexDateDTO::make($name, $value));
    }

    public function dateTime(string $name, ?Carbon $value): self
    {
        return $this->push(IndexDateTimeDTO::make($name, $value));
    }

    /**
     * @param  Collection<int, mixed>|array<int, mixed>  $rows
     */
    public function table(string $name, Collection|array $rows): self
    {
        return $this->push(IndexTableDTO::make($name, $rows));
    }

    public function isEmpty(): bool
    {
        return $this->indexes->isEmpty();
    }

    /**
     * The underlying index objects, as the thin requests expect.
     *
     * @return Collection<int, IndexEntry>
     */
    public function toCollection(): Collection
    {
        return $this->indexes;
    }

    /**
     * @param  IndexEntry  $index
     */
    private function push(object $index): self
    {
        $this->indexes->push($index);

        return $this;
    }
}
