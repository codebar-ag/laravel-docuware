<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyGroups\Group;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\GetGroups;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list groups', function () {
    Event::fake();

    $groups = $this->connector->send(new GetGroups)->dto();

    $this->assertInstanceOf(Collection::class, $groups);

    foreach ($groups as $group) {
        $this->assertInstanceOf(Group::class, $group);
    }

    $this->assertNotCount(0, $groups);
    Event::assertDispatched(DocuWareResponseLog::class);
});
