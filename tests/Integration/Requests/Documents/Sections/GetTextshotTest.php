<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

it('get textshot for a specific section', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    $sections = DocuWare::documents($this->cabinet)->sections((string) $document->id);

    $textshot = DocuWare::documents($this->cabinet)->textshot($sections->first()->id);

    expect(Arr::get($textshot, 'PageCount'))->toBe(1);

    Event::assertDispatched(ResponseReceived::class);
})->group('sections', 'textshot');
