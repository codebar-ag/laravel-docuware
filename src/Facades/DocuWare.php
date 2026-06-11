<?php

namespace CodebarAg\DocuWare\Facades;

use CodebarAg\DocuWare\Client\DocuWareClient;
use CodebarAg\DocuWare\DocuWareManager;
use CodebarAg\DocuWare\DocuWareUrl;
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
use Illuminate\Support\Facades\Facade;

/**
 * @see DocuWareManager
 *
 * The 2.0 entry point. Bare calls operate on the default instance; `instance('tenant')`
 * selects another. Consumers touch resources and data — never Saloon.
 *
 * @method static DocuWareClient instance(string|\CodebarAg\DocuWare\Config\InstanceConfig|null $instance = null)
 * @method static DocuWareClient connection(\CodebarAg\DocuWare\Config\InstanceConfig $config)
 * @method static DocumentsResource documents(string $fileCabinetId)
 * @method static FileCabinetsResource fileCabinets()
 * @method static DialogsResource dialogs(string $fileCabinetId)
 * @method static SelectListsResource selectLists(string $fileCabinetId)
 * @method static OrganizationsResource organizations()
 * @method static UsersResource users()
 * @method static GroupsResource groups()
 * @method static RolesResource roles()
 * @method static WorkflowsResource workflows()
 * @method static TrashResource trash()
 * @method static DocuWareUrl url(string $url, string $username, string $password, ?string $passphrase = null)
 */
class DocuWare extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DocuWareManager::class;
    }
}
