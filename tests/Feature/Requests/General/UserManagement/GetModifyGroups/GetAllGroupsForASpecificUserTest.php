<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyGroups\Group;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\GetAllGroupsForASpecificUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list groups for a specific user', function () {
    Event::fake();

    $users = $this->connector->send(new GetUsers())->dto();

    $groups = $this->connector->send(new GetAllGroupsForASpecificUser($users->get(2)->id))->dto();

    $this->assertInstanceOf(Collection::class, $groups);

    foreach ($groups as $group) {
        $this->assertInstanceOf(Group::class, $group);
    }

    $this->assertNotCount(0, $groups);
    Event::assertDispatched(DocuWareResponseLog::class);
});
