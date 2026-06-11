<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can download a section', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    $sections = DocuWare::documents($this->cabinet)->sections((string) $document->id);

    $contents = DocuWare::documents($this->cabinet)->downloadSection($sections->first()->id);

    expect(strlen($contents))->toBe(strlen('::fake-file-content::'));

    Event::assertDispatched(ResponseReceived::class);
})->group('download');
