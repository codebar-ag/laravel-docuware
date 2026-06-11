<?php

use CodebarAg\DocuWare\Data\Users\GroupData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list groups for a specific user', function () {
    Event::fake();

    $users = DocuWare::users()->all();

    $groups = DocuWare::users()->groupsOf($users->get(2)->id);

    expect($groups)->toBeInstanceOf(Collection::class)
        ->and($groups)->not->toBeEmpty();

    foreach ($groups as $group) {
        expect($group)->toBeInstanceOf(GroupData::class);
    }

    Event::assertDispatched(ResponseReceived::class);
})->group('integration');
