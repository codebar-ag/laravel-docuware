<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\CreateUpdateUser\User;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\CreateUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\AddUserToAGroup;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\RemoveUserFromAGroup;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

it('removes a user from a group', function () {
    Event::fake();

    $timestamp = Str::substr((string) Carbon::now()->timestamp, -8);

    $user = $this->connector->send(new CreateUser(new User(
        name: $timestamp.' - Test User',
        dbName: $timestamp,
        email: $timestamp.'-test@example.test',
        password: 'TESTPASSWORD',
    )))->dto();

    Sleep::for(5)->seconds();

    $this->connector->send(new AddUserToAGroup(
        userId: $user->id,
        ids: [(string) config('laravel-docuware.tests.group_id')],
    ))->dto();

    Event::fake();

    Sleep::for(5)->seconds();

    $response = $this->connector->send(new RemoveUserFromAGroup(
        userId: $user->id,
        ids: [(string) config('laravel-docuware.tests.group_id')],
    ))->dto();

    expect($response->status())->toBe(200);

    Event::assertDispatched(DocuWareResponseLog::class);
});
