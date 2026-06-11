<?php

use CodebarAg\DocuWare\Data\Users\UserData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can get user by id', function () {
    Event::fake();

    $users = DocuWare::users()->all();

    $user = DocuWare::users()->find($users->get(2)->id);

    expect($user)->toBeInstanceOf(UserData::class);

    Event::assertDispatched(ResponseReceived::class);
})->group('integration');
