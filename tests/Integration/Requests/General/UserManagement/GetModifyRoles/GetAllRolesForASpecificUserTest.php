<?php

use CodebarAg\DocuWare\Data\Users\RoleData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list roles for a specific user', function () {
    Event::fake();

    $users = DocuWare::users()->all();

    $roles = DocuWare::users()->rolesOf($users->get(2)->id);

    expect($roles)->toBeInstanceOf(Collection::class)
        ->and($roles)->not->toBeEmpty();

    foreach ($roles as $role) {
        expect($role)->toBeInstanceOf(RoleData::class);
    }

    Event::assertDispatched(ResponseReceived::class);
})->group('integration');
