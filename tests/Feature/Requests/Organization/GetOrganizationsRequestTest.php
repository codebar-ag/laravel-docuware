<?php

use CodebarAg\DocuWare\DTO\OrganizationIndex;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Organization\GetOrganizationsRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    $this->connector = getConnector();
});

it('can list organizations', function () {
    Event::fake();

    $organizations = $this->connector->send(new GetOrganizationsRequest())->dto();

    $this->assertInstanceOf(Collection::class, $organizations);

    foreach ($organizations as $organization) {
        $this->assertInstanceOf(OrganizationIndex::class, $organization);
    }

    $this->assertNotCount(0, $organizations);
    Event::assertDispatched(DocuWareResponseLog::class);
});
