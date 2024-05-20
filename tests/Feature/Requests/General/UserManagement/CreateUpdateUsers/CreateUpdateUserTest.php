<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\CreateUpdateUser\User;
use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User as GetUser;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\CreateUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\UpdateUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

it('can create users', function () {
    Event::fake();

    $timestamp = Str::substr(Carbon::now()->timestamp, -8);

    $user = $this->connector->send(new CreateUser(new User(
        name: $timestamp.' - Test User',
        dbName: $timestamp,
        email: $timestamp.'-test@example.test',
        password: 'TESTPASSWORD',
    )))->dto();

    $this->assertInstanceOf(GetUser::class, $user);

    Event::assertDispatched(DocuWareResponseLog::class);

    return $user;
});

it('can update users', function ($user) {
    Event::fake();

    sleep(5);

    $user->name .= ' - Updated';
    $user->active = false;

    $user = $this->connector->send(new UpdateUser($user))->dto();

    $this->assertInstanceOf(GetUser::class, $user);

    Event::assertDispatched(DocuWareResponseLog::class);
})->depends('it can create users');
