<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyRoles\Role;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\GetAllRolesForASpecificUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list groups', function () {
    Event::fake();

    $users = $this->connector->send(new GetUsers)->dto();

    $roles = $this->connector->send(new GetAllRolesForASpecificUser($users->get(2)->id))->dto();

    $this->assertInstanceOf(Collection::class, $roles);

    foreach ($roles as $role) {
        $this->assertInstanceOf(Role::class, $role);
    }

    $this->assertNotCount(0, $roles);
    Event::assertDispatched(DocuWareResponseLog::class);
});
