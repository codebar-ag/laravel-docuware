<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\GetGroups;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsersOfAGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list users of a group', function () {
    Event::fake();

    $groups = $this->connector->send(new GetGroups)->dto();

    $users = $this->connector->send(new GetUsersOfAGroup($groups->first()->id))->dto();

    $this->assertInstanceOf(Collection::class, $users);

    foreach ($users as $user) {
        $this->assertInstanceOf(User::class, $user);
    }

    $this->assertNotCount(0, $users);
    Event::assertDispatched(DocuWareResponseLog::class);
});
