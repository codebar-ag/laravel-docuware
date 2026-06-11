<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('transfers a document to another file cabinet or basket', function () {
    $destinationId = config('laravel-docuware.tests.basket_id');

    if (! is_string($destinationId) || $destinationId === '') {
        test()->markTestSkipped('No transfer destination (basket) configured.');
    }

    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    Sleep::for(2)->seconds();

    $ok = DocuWare::documents($this->cabinet)->transfer(
        documentId: (string) $document->id,
        destinationFileCabinetId: $destinationId,
    );

    expect($ok)->toBeTrue();

    Event::assertDispatched(ResponseReceived::class);
});
