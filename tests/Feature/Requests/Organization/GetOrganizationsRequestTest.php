<?php

use CodebarAg\DocuWare\DTO\General\Organization\Organization;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list organizations', function () {
    Event::fake();

    $organizations = $this->connector->send(new GetOrganization)->dto();

    $this->assertInstanceOf(Collection::class, $organizations);

    foreach ($organizations as $organization) {
        $this->assertInstanceOf(Organization::class, $organization);
    }

    $this->assertNotCount(0, $organizations);
    Event::assertDispatched(DocuWareResponseLog::class);
});
