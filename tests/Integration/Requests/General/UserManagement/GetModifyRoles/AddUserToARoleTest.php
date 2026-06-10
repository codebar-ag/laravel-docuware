<?php

use CodebarAg\DocuWare\DTO\General\UserManagement\CreateUpdateUser\User;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\CreateUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyRoles\AddUserToARole;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

it('adds a user to a role', function () {
    Event::fake();

    $timestamp = Str::substr((string) Carbon::now()->timestamp, -8);

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
            (string) config('laravel-docuware.tests.role_id'),
        ]
    ))->dto();

    expect($response->status())->toBe(200);

    Event::assertDispatched(DocuWareResponseLog::class);
});
