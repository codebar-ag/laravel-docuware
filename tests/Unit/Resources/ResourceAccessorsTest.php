<?php

use CodebarAg\DocuWare\Facades\DocuWare;
use CodebarAg\DocuWare\Requests\FileCabinets\SelectLists\GetSelectLists;
use CodebarAg\DocuWare\Resources\DialogsResource;
use CodebarAg\DocuWare\Resources\DocumentsResource;
use CodebarAg\DocuWare\Resources\FileCabinetsResource;
use CodebarAg\DocuWare\Resources\GroupsResource;
use CodebarAg\DocuWare\Resources\OrganizationsResource;
use CodebarAg\DocuWare\Resources\RolesResource;
use CodebarAg\DocuWare\Resources\SelectListsResource;
use CodebarAg\DocuWare\Resources\UsersResource;
use CodebarAg\DocuWare\Resources\WorkflowsResource;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    config()->set('laravel-docuware.default', 'default');
    config()->set('laravel-docuware.instances.default', [
        'grant' => 'credentials',
        'url' => 'https://example.docuware.cloud',
        'username' => 'alice',
        'password' => 'secret',
    ]);
    config()->set('laravel-docuware.configurations.cache.driver', 'array');
    config()->set('laravel-docuware.configurations.cache.lifetime_in_seconds', 60);
    config()->set('laravel-docuware.configurations.request.timeout_in_seconds', 30);
    config()->set('laravel-docuware.configurations.client_id', 'x');
    config()->set('laravel-docuware.configurations.scope', 'y');
});

it('exposes every resource through the facade', function () {
    expect(DocuWare::documents('cab'))->toBeInstanceOf(DocumentsResource::class)
        ->and(DocuWare::fileCabinets())->toBeInstanceOf(FileCabinetsResource::class)
        ->and(DocuWare::dialogs('cab'))->toBeInstanceOf(DialogsResource::class)
        ->and(DocuWare::selectLists('cab'))->toBeInstanceOf(SelectListsResource::class)
        ->and(DocuWare::organizations())->toBeInstanceOf(OrganizationsResource::class)
        ->and(DocuWare::users())->toBeInstanceOf(UsersResource::class)
        ->and(DocuWare::groups())->toBeInstanceOf(GroupsResource::class)
        ->and(DocuWare::roles())->toBeInstanceOf(RolesResource::class)
        ->and(DocuWare::workflows())->toBeInstanceOf(WorkflowsResource::class);
});

it('reads select-list values from a real fixture', function () {
    $client = clientWithMock([
        GetSelectLists::class => MockResponse::fixture('file-cabinets/select-lists/get-select-lists'),
    ]);

    $values = $client->selectLists('cab')->get('dialog-1', 'DOCUMENT_TYPE');

    expect($values)->toBeInstanceOf(Collection::class);
});
