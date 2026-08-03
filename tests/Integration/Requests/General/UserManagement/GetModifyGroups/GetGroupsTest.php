<?php

use CodebarAg\DocuWare\Data\Users\GroupData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list groups', function () {
    Event::fake();

    $groups = DocuWare::groups()->all();

    expect($groups)->toBeInstanceOf(Collection::class)
        ->and($groups)->not->toBeEmpty();

    foreach ($groups as $group) {
        expect($group)->toBeInstanceOf(GroupData::class);
    }

    Event::assertDispatched(ResponseReceived::class);
})->group('integration');
