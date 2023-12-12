<?php

use CodebarAg\DocuWare\DTO\Organization;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Organization\GetOrganizationRequest;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    $this->connector = getConnector();
});

it('can get an organization', function () {
    Event::fake();

    $orgID = config('laravel-docuware.tests.organization_id');

    $organization = $this->connector->send(new GetOrganizationRequest($orgID))->dto();

    $this->assertInstanceOf(Organization::class, $organization);
    Event::assertDispatched(DocuWareResponseLog::class);
});
