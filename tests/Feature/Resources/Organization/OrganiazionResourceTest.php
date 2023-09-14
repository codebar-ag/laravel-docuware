<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\DTO\Configuration;
use CodebarAg\DocuWare\DTO\Organization;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Organization\GetOrganizationsRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

it('can list organizations', function () {
    Event::fake();

    $configuration = new Configuration(
        config('docuware.credentials.url'),
        config('docuware.credentials.username'),
        config('docuware.credentials.username'),
        config('docuware.passphrase'),
        config('docuware.cookie'),
        null,
        config('docuware.timeout'),
        config('docuware.cache_driver'),
        config('docuware.cache_driver'),
        []
    );

    $connector = new DocuWareConnector();
    $request = new GetOrganizationsRequest();

    $response = $connector->send($request);

    dd($response);
    //$organizations = (new DocuWare())->getOrganizations();

    $this->assertInstanceOf(Collection::class, $organizations);
    $this->assertNotCount(0, $organizations);
    Event::assertDispatched(DocuWareResponseLog::class);

})->only();

it('can get an organization', function () {
    Event::fake();

    $orgID = config('docuware.tests.organization_id');

    $organization = (new DocuWare())->getOrganization($orgID);

    $this->assertInstanceOf(Organization::class, $organization);
    Event::assertDispatched(DocuWareResponseLog::class);
});
