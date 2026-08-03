<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can delete a specific section', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    $sections = DocuWare::documents($this->cabinet)->sections((string) $document->id);

    DocuWare::documents($this->cabinet)->deleteSection($sections->first()->id);

    Event::assertDispatched(ResponseReceived::class);
})->group('sections');
