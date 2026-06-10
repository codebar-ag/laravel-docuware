<?php

namespace CodebarAg\DocuWare\DTO\Documents\Annotations;

/**
 * A drawing annotation placed inside an annotation layer (text, rectangle, line, polyline,
 * or a delete marker). Stamps are placed differently — see {@see StampPlacement}.
 */
interface AnnotationEntry
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
