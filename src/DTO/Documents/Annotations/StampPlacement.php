<?php

namespace CodebarAg\DocuWare\DTO\Documents\Annotations;

/**
 * Places a stamp on a document. Provide a {@see Point} location for "Add Stamp With Position",
 * or leave it null for "Add Stamp With Best Position".
 */
final class StampPlacement
{
    /**
     * @param  list<StampField>  $fields
     */
    public function __construct(
        public string $stampId,
        public int $layer = 1,
        public ?Point $location = null,
        public array $fields = [],
        public ?string $password = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $placement = ['$type' => 'StampPlacement'];

        if ($this->location !== null) {
            // DocuWare expects the stamp X/Y as strings.
            $placement['Location'] = [
                'X' => (string) $this->location->x,
                'Y' => (string) $this->location->y,
            ];
        }

        $placement['StampId'] = $this->stampId;
        $placement['Layer'] = $this->layer;
        $placement['Field'] = array_map(static fn (StampField $field): array => $field->toArray(), $this->fields);
        $placement['Password'] = $this->password;

        return $placement;
    }
}
