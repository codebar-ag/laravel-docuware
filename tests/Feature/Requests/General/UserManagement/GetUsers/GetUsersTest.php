<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list users', function () {
    Event::fake();

    $users = $this->connector->send(new GetUsers())->dto();

    $this->assertInstanceOf(Collection::class, $users);

    foreach ($users as $user) {
        $this->assertInstanceOf(User::class, $user);
    }

    $this->assertNotCount(0, $users);
    Event::assertDispatched(DocuWareResponseLog::class);
});
