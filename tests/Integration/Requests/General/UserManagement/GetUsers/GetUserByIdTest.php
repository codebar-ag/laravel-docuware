<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUserById;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsers;
use Illuminate\Support\Facades\Event;

it('can get user by id', function () {
    Event::fake();

    $users = $this->connector->send(new GetUsers)->dto();

    $user = $this->connector->send(new GetUserById($users->get(2)->id))->dto();

    $this->assertInstanceOf(User::class, $user);

    Event::assertDispatched(DocuWareResponseLog::class);
});
