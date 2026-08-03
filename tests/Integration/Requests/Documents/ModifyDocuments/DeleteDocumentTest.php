<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('deletes a document', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    Sleep::for(2)->seconds();

    DocuWare::documents($this->cabinet)->delete($document->id);

    Event::assertDispatched(ResponseReceived::class);
});
