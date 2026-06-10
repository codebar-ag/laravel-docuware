<?php

namespace CodebarAg\DocuWare\DTO\Documents\Annotations;

/**
 * Fluently assembles the nested payload for POST …/Documents/{id}/Annotation so callers do not
 * have to hand-build the Annotations → AnnotationsPlacement → Items → Layer → Items structure.
 *
 * Stamps ({@see StampPlacement}) become their own placement items; drawing entries
 * ({@see TextEntry}, {@see RectEntry}, {@see LineEntry}, {@see PolyLineEntry}, {@see DeleteEntry})
 * are grouped into an "Annotation" placement item under their layer id.
 *
 * Example:
 *   $payload = AnnotationBuilder::make()
 *       ->addEntry(new TextEntry('Hello', Location::make(100, 100, 1500, 500)))
 *       ->toArray();
 *   $connector->send(new AddDocumentAnnotations($cabinetId, $documentId, $payload));
 */
final class AnnotationBuilder
{
    /** @var list<array<string, mixed>> */
    private array $stampItems = [];

    /** @var array<string, list<array<string, mixed>>> */
    private array $layers = [];

    public function __construct(
        private int $pageNumber = 0,
        private int $sectionNumber = 0,
    ) {}

    public static function make(int $pageNumber = 0, int $sectionNumber = 0): self
    {
        return new self($pageNumber, $sectionNumber);
    }

    public function page(int $pageNumber): self
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    public function section(int $sectionNumber): self
    {
        $this->sectionNumber = $sectionNumber;

        return $this;
    }

    public function addStamp(StampPlacement $stamp): self
    {
        $this->stampItems[] = $stamp->toArray();

        return $this;
    }

    public function addEntry(AnnotationEntry $entry, string $layerId = '1'): self
    {
        $this->layers[$layerId][] = $entry->toArray();

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $items = $this->stampItems;

        if ($this->layers !== []) {
            $layer = [];
            foreach ($this->layers as $id => $entries) {
                $layer[] = [
                    // PHP coerces numeric string array keys to int; DocuWare expects a string Id.
                    'Id' => (string) $id,
                    'Items' => $entries,
                ];
            }

            $items[] = [
                '$type' => 'Annotation',
                'Layer' => $layer,
            ];
        }

        return [
            'Annotations' => [
                [
                    'PageNumber' => $this->pageNumber,
                    'SectionNumber' => $this->sectionNumber,
                    'AnnotationsPlacement' => [
                        'Items' => $items,
                    ],
                ],
            ],
        ];
    }
}
