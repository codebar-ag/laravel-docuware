<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('returns annotations for a document', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    Sleep::for(2)->seconds();

    $annotations = DocuWare::documents($this->cabinet)->annotations($document->id);

    expect($annotations)->not->toBeNull();

    Event::assertDispatched(ResponseReceived::class);
});
