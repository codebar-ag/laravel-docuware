<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('requests an organization login token', function () {
    Event::fake();

    $token = DocuWare::organizations()->loginToken(
        ['PlatformService'],
        'Multi',
        '1.00:00:00',
    );

    expect($token)->toBeString()->not->toBeEmpty();

    Event::assertDispatched(ResponseReceived::class);
})->group('integration');
