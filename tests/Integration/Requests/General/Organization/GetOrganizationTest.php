<?php

use CodebarAg\DocuWare\Data\Organization\OrganizationData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list organizations', function () {
    Event::fake();

    $organizations = DocuWare::organizations()->all();

    expect($organizations)->toBeInstanceOf(Collection::class)
        ->and($organizations)->not->toBeEmpty();

    foreach ($organizations as $organization) {
        expect($organization)->toBeInstanceOf(OrganizationData::class);
    }

    Event::assertDispatched(ResponseReceived::class);
})->group('integration');
