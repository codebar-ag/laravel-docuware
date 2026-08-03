<?php

use CodebarAg\DocuWare\Data\Users\UserData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list users', function () {
    Event::fake();

    $users = DocuWare::users()->all();

    expect($users)->toBeInstanceOf(Collection::class)
        ->and($users)->not->toBeEmpty();

    foreach ($users as $user) {
        expect($user)->toBeInstanceOf(UserData::class);
    }

    Event::assertDispatched(ResponseReceived::class);
})->group('integration');
