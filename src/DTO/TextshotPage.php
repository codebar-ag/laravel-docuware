<?php

namespace CodebarAg\DocuWare\DTO;

use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class TextshotPage
{
    /**
     * @param  Collection<int, array<string, mixed>>  $collection
     * @return Collection<int, TextshotPage>
     */
    public static function fromCollection(Collection $collection): Collection
    {
        return $collection->map(fn (array $data) => self::fromJson($data));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        $rawItems = Arr::get($data, 'Items', []);

        return new self(
            language: Arr::get($data, 'Lang'),
            content: self::content(JsonArrays::listOfRecords(is_array($rawItems) ? $rawItems : []))
        );
    }

    public function __construct(
        public ?string $language,
        public string $content,
    ) {}

    /**
     * @param  list<array<string, mixed>>  $rawItems
     */
    protected static function content(array $rawItems): string
    {
        return collect($rawItems)
            ->filter(function (mixed $item) {
                return Arr::get($item, '$type') === 'TextZone';
            })
            ->pluck('Ln')
            ->flatten(2)
            ->filter(function (mixed $item) {
                return is_array($item);
            })
            ->flatten(1)
            ->map(function (mixed $item) {

                $type = Arr::get($item, '$type');

                return match ($type) {
                    'Word' => Arr::get($item, 'Value'),
                    default => null,
                };
            })->implode(' ');
    }
}
