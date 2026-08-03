<?php

use CodebarAg\DocuWare\Data\Write\UserInput;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

it('adds a user to a role', function () {
    Event::fake();

    $timestamp = Str::substr((string) Carbon::now()->timestamp, -8);

    $user = DocuWare::users()->create(UserInput::make(
        name: $timestamp.' - Test User',
        dbName: $timestamp,
        email: $timestamp.'-test@example.test',
        password: 'TestPass123!',
        networkId: null,
    ));

    Sleep::for(5)->seconds();

    $roleId = (string) config('laravel-docuware.tests.role_id');

    DocuWare::users()->addToRole($user->id, [$roleId]);

    $roleIds = DocuWare::users()->rolesOf($user->id)->pluck('id');

    expect($roleIds)->toContain($roleId);

    Event::assertDispatched(ResponseReceived::class);
})->group('integration');
