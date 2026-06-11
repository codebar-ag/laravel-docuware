<?php

namespace CodebarAg\DocuWare\Resources;

use CodebarAg\DocuWare\Data\Documents\DeleteDocumentsData;
use CodebarAg\DocuWare\Data\Documents\RestoreDocumentsData;
use CodebarAg\DocuWare\Data\Documents\TrashPageData;
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\DeleteDocuments;
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\GetDocuments;
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\RestoreDocuments;
use Illuminate\Support\Collection;

/**
 * The organization trash bin. Reached via `DocuWare::trash()`.
 */
final class TrashResource extends Resource
{
    /**
     * One page of trash-bin documents.
     */
    public function search(int $page = 1, int $perPage = 50, ?string $searchTerm = null): TrashPageData
    {
        $response = $this->send(new GetDocuments(
            page: $page,
            perPage: $perPage,
            searchTerm: $searchTerm,
        ));

        return TrashPageData::fromDocuWare($response->json(), $page, $perPage);
    }

    /**
     * Permanently delete documents from the trash bin.
     *
     * @param  array<int, mixed>|Collection<int, mixed>  $ids
     */
    public function delete(array|Collection $ids): DeleteDocumentsData
    {
        $response = $this->send(new DeleteDocuments($ids));

        return DeleteDocumentsData::fromDocuWare($response->json());
    }

    /**
     * Restore documents from the trash bin back to their cabinet.
     *
     * @param  array<int, mixed>|Collection<int, mixed>  $ids
     */
    public function restore(array|Collection $ids): RestoreDocumentsData
    {
        $response = $this->send(new RestoreDocuments($ids));

        return RestoreDocumentsData::fromDocuWare($response->json());
    }
}
