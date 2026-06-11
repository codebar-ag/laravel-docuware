<?php

use CodebarAg\DocuWare\Data\Users\UserData;
use CodebarAg\DocuWare\Data\Write\UserInput;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

it('creates a user', function () {
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
})->group('integration');
