<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can download a thumbnail', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    $sections = DocuWare::documents($this->cabinet)->sections((string) $document->id);

    $contents = DocuWare::documents($this->cabinet)->thumbnail($sections->first()->id);

    expect($contents)->toBeString()
        ->and(strlen($contents))->toBeGreaterThan(0);

    Event::assertDispatched(ResponseReceived::class);
})->group('download');
