<?php

use CodebarAg\DocuWare\Data\Write\UserInput;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

it('adds a user to a group', function () {
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

    $groupId = (string) config('laravel-docuware.tests.group_id');

    DocuWare::users()->addToGroup($user->id, [$groupId]);

    $groupIds = DocuWare::users()->groupsOf($user->id)->pluck('id');

    expect($groupIds)->toContain($groupId);

    Event::assertDispatched(ResponseReceived::class);
})->group('integration');
