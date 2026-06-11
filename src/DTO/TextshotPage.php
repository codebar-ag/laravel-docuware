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
            content: self::content(JsonArrays::listOfRecords(is_array($rawItems) ? $rawItems : [])),
            schemaType: Arr::get($data, '$type'),
            version: Arr::get($data, 'Version'),
            horizontalDpi: Arr::get($data, 'HorizontalDpi'),
            verticalDpi: Arr::get($data, 'VerticalDpi'),
            sizeX: Arr::get($data, 'SizeX'),
            sizeY: Arr::get($data, 'SizeY'),
            skewAngle: ($skew = Arr::get($data, 'SkewAngle')) === null ? null : (float) $skew,
            rotation: Arr::get($data, 'Rotation'),
            languageDetection: Arr::get($data, 'LanguageDetection'),
            candidateDetectionVersion: Arr::get($data, 'CandidateDetectionVersion'),
            barCodes: Arr::get($data, 'BarCodes'),
            candidates: Arr::get($data, 'Candidates'),
            metadata: Arr::get($data, 'metadata'),
        );
    }

    /**
     * @param  list<array<string, mixed>>|null  $barCodes
     * @param  list<array<string, mixed>>|null  $candidates
     * @param  list<array<string, mixed>>|null  $metadata
     */
    public function __construct(
        public readonly ?string $language,
        public readonly string $content,
        public readonly ?string $schemaType = null,
        public readonly ?int $version = null,
        public readonly ?float $horizontalDpi = null,
        public readonly ?float $verticalDpi = null,
        public readonly ?int $sizeX = null,
        public readonly ?int $sizeY = null,
        public readonly ?float $skewAngle = null,
        public readonly ?string $rotation = null,
        public readonly ?string $languageDetection = null,
        public readonly ?int $candidateDetectionVersion = null,
        public readonly ?array $barCodes = null,
        public readonly ?array $candidates = null,
        public readonly ?array $metadata = null,
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
