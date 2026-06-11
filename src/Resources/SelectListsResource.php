<?php

namespace CodebarAg\DocuWare\Resources;

use CodebarAg\DocuWare\Client\DocuWareClient;
use CodebarAg\DocuWare\Requests\FileCabinets\SelectLists\GetFilteredSelectLists;
use CodebarAg\DocuWare\Requests\FileCabinets\SelectLists\GetSelectLists;
use Illuminate\Support\Collection;

/**
 * Select-list values for a cabinet's index fields. Reached via
 * `DocuWare::selectLists($cabinetId)`.
 */
final class SelectListsResource extends Resource
{
    public function __construct(
        DocuWareClient $client,
        private readonly string $fileCabinetId,
    ) {
        parent::__construct($client);
    }

    /**
     * All values of a field's select list.
     *
     * @return Collection<int, mixed>
     */
    public function get(string $dialogId, string $fieldName): Collection
    {
        $response = $this->send(new GetSelectLists($this->fileCabinetId, $dialogId, $fieldName));

        return $this->values($response->json('Value', []));
    }

    /**
     * Values filtered by a dialog expression (cascading / dependent select lists).
     *
     * @param  array<int|string, mixed>  $dialogExpression
     * @return Collection<int, mixed>
     */
    public function filtered(string $dialogId, string $fieldName, array $dialogExpression): Collection
    {
        $response = $this->send(new GetFilteredSelectLists($this->fileCabinetId, $dialogId, $fieldName, $dialogExpression));

        return $this->values($response->json('Value', []));
    }

    /**
     * @return Collection<int, mixed>
     */
    private function values(mixed $raw): Collection
    {
        return collect(is_array($raw) ? $raw : [])->values();
    }
}
