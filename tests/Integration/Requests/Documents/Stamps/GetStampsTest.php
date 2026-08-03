<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('lists stamp definitions for a file cabinet', function () {
    Event::fake();

    $stamps = DocuWare::documents($this->cabinet)->stamps();

    expect($stamps)->not->toBeNull();

    Event::assertDispatched(ResponseReceived::class);
});
