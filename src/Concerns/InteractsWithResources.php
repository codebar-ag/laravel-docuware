<?php

namespace CodebarAg\DocuWare\Concerns;

use CodebarAg\DocuWare\Client\DocuWareClient;
use CodebarAg\DocuWare\DocuWareManager;
use CodebarAg\DocuWare\Resources\DialogsResource;
use CodebarAg\DocuWare\Resources\DocumentsResource;
use CodebarAg\DocuWare\Resources\FileCabinetsResource;
use CodebarAg\DocuWare\Resources\GroupsResource;
use CodebarAg\DocuWare\Resources\OrganizationsResource;
use CodebarAg\DocuWare\Resources\RolesResource;
use CodebarAg\DocuWare\Resources\SelectListsResource;
use CodebarAg\DocuWare\Resources\TrashResource;
use CodebarAg\DocuWare\Resources\UsersResource;
use CodebarAg\DocuWare\Resources\WorkflowsResource;

/**
 * The public resource accessors, shared by {@see DocuWareClient} (acts on itself) and
 * {@see DocuWareManager} (delegates to the default instance). This is the surface consumers
 * reach through the `DocuWare` facade.
 */
trait InteractsWithResources
{
    abstract protected function resourceClient(): DocuWareClient;

    /**
     * Documents within a file cabinet: search, find, download, store, annotate…
     */
    public function documents(string $fileCabinetId): DocumentsResource
    {
        return new DocumentsResource($this->resourceClient(), $fileCabinetId);
    }

    /**
     * File cabinets and document trays.
     */
    public function fileCabinets(): FileCabinetsResource
    {
        return new FileCabinetsResource($this->resourceClient());
    }

    /**
     * Dialogs (search / store / result …) of a file cabinet.
     */
    public function dialogs(string $fileCabinetId): DialogsResource
    {
        return new DialogsResource($this->resourceClient(), $fileCabinetId);
    }

    /**
     * Select-list values for a cabinet's index fields.
     */
    public function selectLists(string $fileCabinetId): SelectListsResource
    {
        return new SelectListsResource($this->resourceClient(), $fileCabinetId);
    }

    /**
     * Organizations the authenticated user can access.
     */
    public function organizations(): OrganizationsResource
    {
        return new OrganizationsResource($this->resourceClient());
    }

    /**
     * Organization users.
     */
    public function users(): UsersResource
    {
        return new UsersResource($this->resourceClient());
    }

    /**
     * Organization groups.
     */
    public function groups(): GroupsResource
    {
        return new GroupsResource($this->resourceClient());
    }

    /**
     * Organization roles.
     */
    public function roles(): RolesResource
    {
        return new RolesResource($this->resourceClient());
    }

    /**
     * Workflow instance history.
     */
    public function workflows(): WorkflowsResource
    {
        return new WorkflowsResource($this->resourceClient());
    }

    /**
     * The trash bin (delete / restore).
     */
    public function trash(): TrashResource
    {
        return new TrashResource($this->resourceClient());
    }
}
