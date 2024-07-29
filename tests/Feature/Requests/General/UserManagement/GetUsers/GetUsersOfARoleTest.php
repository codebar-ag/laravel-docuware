<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\GetRoles;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsersOfARole;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list users of a role', function () {
    Event::fake();

    $roles = $this->connector->send(new GetRoles)->dto();

    $users = $this->connector->send(new GetUsersOfARole($roles->first()->id))->dto();

    $this->assertInstanceOf(Collection::class, $users);

    foreach ($users as $user) {
        $this->assertInstanceOf(User::class, $user);
    }

    $this->assertNotCount(0, $users);
    Event::assertDispatched(DocuWareResponseLog::class);
});
