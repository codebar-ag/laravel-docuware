<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\CreateUpdateUser\User;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\CreateUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\AddUserToAGroup;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\RemoveUserFromAGroup;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can add groups to a user', function () {
    Event::fake();

    $timestamp = Str::substr(Carbon::now()->timestamp, -8);

    $user = $this->connector->send(new CreateUser(new User(
        name: $timestamp.' - Test User',
        dbName: $timestamp,
        email: $timestamp.'-test@example.test',
        password: 'TESTPASSWORD',
    )))->dto();

    Sleep::for(5)->seconds();

    $response = $this->connector->send(new AddUserToAGroup(
        userId: $user->id,
        ids: [
            env('DOCUWARE_TESTS_GROUP_ID'),
        ]
    ))->dto();

    expect($response->status())->toBe(200);

    Event::assertDispatched(DocuWareResponseLog::class);

    return $user;
});

it('can remove groups to a user', function ($user) {
    Event::fake();

    Sleep::for(5)->seconds();

    $response = $this->connector->send(new RemoveUserFromAGroup(
        userId: $user->id,
        ids: [
            env('DOCUWARE_TESTS_GROUP_ID'),
        ]
    ))->dto();

    expect($response->status())->toBe(200);

    Event::assertDispatched(DocuWareResponseLog::class);
})->depends('it can add groups to a user');
