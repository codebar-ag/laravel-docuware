<?php

namespace CodebarAg\DocuWare\DTO\Documents\Annotations;

final class DeleteEntry implements AnnotationEntry
{
    public function __construct(
        public string $id,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            '$type' => 'DeleteEntry',
            'Id' => $this->id,
        ];
    }
}
