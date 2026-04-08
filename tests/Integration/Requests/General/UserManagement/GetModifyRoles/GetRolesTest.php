<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyRoles\Role;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\GetRoles;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list groups', function () {
    Event::fake();

    $roles = $this->connector->send(new GetRoles)->dto();

    $this->assertInstanceOf(Collection::class, $roles);

    foreach ($roles as $role) {
        $this->assertInstanceOf(Role::class, $role);
    }

    $this->assertNotCount(0, $roles);
    Event::assertDispatched(DocuWareResponseLog::class);
});
