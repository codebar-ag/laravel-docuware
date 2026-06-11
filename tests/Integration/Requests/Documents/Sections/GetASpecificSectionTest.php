<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can get a specific section', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    $sections = DocuWare::documents($this->cabinet)->sections((string) $document->id);

    $section = DocuWare::documents($this->cabinet)->section($sections->first()->id);

    expect($section->id)->toBe($sections->first()->id);

    Event::assertDispatched(ResponseReceived::class);
})->group('sections');
