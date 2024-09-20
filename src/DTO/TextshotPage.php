<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class TextshotPage
{
    public static function fromCollection(Collection $collection): Collection
    {
        return $collection->map(fn (array $data) => self::fromJson($data));
    }

    public static function fromJson(array $data): self
    {
        $rawItems = Arr::get($data, 'Items', []);

        return new self(
            language: Arr::get($data, 'Lang'),
            content: self::content($rawItems)
        );
    }

    public function __construct(
        public string $language,
        public string $content,
    ) {}

    protected static function content(array $rawItems): string
    {
        return collect($rawItems)
            ->filter(function ($item) {
                return Arr::get($item, '$type') === 'TextZone';
            })
            ->pluck('Ln')
            ->flatten(2)
            ->filter(function ($item) {
                return is_array($item);
            })
            ->flatten(1)
            ->map(function ($item) {

                $type = Arr::get($item, '$type');

                return match ($type) {
                    'Word' => Arr::get($item, 'Value'),
                    default => null,
                };
            })->implode(' ');
    }
}
