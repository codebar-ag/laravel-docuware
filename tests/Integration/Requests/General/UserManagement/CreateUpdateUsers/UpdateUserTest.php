<?php

use CodebarAg\DocuWare\Data\Users\UserData;
use CodebarAg\DocuWare\Data\Write\UserInput;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

it('updates a user', function () {
    Event::fake();

    $timestamp = Str::substr((string) Carbon::now()->timestamp, -8);

    $user = DocuWare::users()->create(UserInput::make(
        name: $timestamp.' - Test User',
        dbName: $timestamp,
        email: $timestamp.'-test@example.test',
        password: 'TestPass123!',
        networkId: null,
    ));

    expect($user)->toBeInstanceOf(UserData::class);

    Event::assertDispatched(ResponseReceived::class);

    Event::fake();

    Sleep::for(2)->seconds();

    $updated = DocuWare::users()->update($user->copyWith(
        name: $user->name.' - Updated',
        active: false,
    ));

    expect($updated)->toBeInstanceOf(UserData::class)
        ->and($updated->name)->toContain('Updated');

    Event::assertDispatched(ResponseReceived::class);
})->group('integration');
