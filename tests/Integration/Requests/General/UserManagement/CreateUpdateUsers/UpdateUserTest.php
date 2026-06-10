<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\CreateUpdateUser\User;
use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User as GetUser;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\CreateUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\UpdateUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

it('updates a user', function () {
    Event::fake();

    $timestamp = Str::substr((string) Carbon::now()->timestamp, -8);

    $user = $this->connector->send(new CreateUser(new User(
        name: $timestamp.' - Test User',
        dbName: $timestamp,
        email: $timestamp.'-test@example.test',
        password: 'TESTPASSWORD',
    )))->dto();

    expect($user)->toBeInstanceOf(GetUser::class);

    Event::assertDispatched(DocuWareResponseLog::class);

    Event::fake();

    Sleep::for(2)->seconds();

    $user->name .= ' - Updated';
    $user->active = false;

    $updated = $this->connector->send(new UpdateUser($user))->dto();

    expect($updated)->toBeInstanceOf(GetUser::class)
        ->and($updated->name)->toContain('Updated');

    Event::assertDispatched(DocuWareResponseLog::class);
});
