<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\CreateUpdateUser\User;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\CreateUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\AddUserToARole;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\RemoveUserFromARole;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can add roles to a user', function () {
    Event::fake();

    $timestamp = Str::substr(Carbon::now()->timestamp, -8);

    $user = $this->connector->send(new CreateUser(new User(
        name: $timestamp.' - Test User',
        dbName: $timestamp,
        email: $timestamp.'-test@example.test',
        password: 'TESTPASSWORD',
    )))->dto();

    Sleep::for(5)->seconds();

    $response = $this->connector->send(new AddUserToARole(
        userId: $user->id,
        ids: [
            env('DOCUWARE_TESTS_ROLE_ID'),
        ]
    ))->dto();

    expect($response->status())->toBe(200);

    Event::assertDispatched(DocuWareResponseLog::class);

    return $user;
});

it('can remove roles to a user', function ($user) {
    Event::fake();

    Sleep::for(5)->seconds();

    $response = $this->connector->send(new RemoveUserFromARole(
        userId: $user->id,
        ids: [
            env('DOCUWARE_TESTS_ROLE_ID'),
        ]
    ))->dto();

    expect($response->status())->toBe(200);

    Event::assertDispatched(DocuWareResponseLog::class);
})->depends('it can add roles to a user');
