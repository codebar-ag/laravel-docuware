<?php

namespace CodebarAg\DocuWare\Data;

use Illuminate\Support\Arr;

/**
 * A field suggestion returned alongside search results when suggestions are requested.
 */
final class SuggestionFieldData extends DocuWareData
{
    /**
     * @param  array<int|string, mixed>  $value
     */
    public function __construct(
        public array $value,
        public ?string $name,
        public ?string $db_name,
        public ?string $confidence,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            value: (array) Arr::get($data, 'Value', []),
            name: Arr::get($data, 'Name'),
            db_name: Arr::get($data, 'DBName'),
            confidence: Arr::get($data, 'Confidence'),
        );
    }
}
