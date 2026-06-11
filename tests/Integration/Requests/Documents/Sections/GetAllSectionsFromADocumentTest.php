<?php

use CodebarAg\DocuWare\Data\SectionData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can get all sections from a document', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    $sections = DocuWare::documents($this->cabinet)->sections((string) $document->id);

    expect($sections)->toBeInstanceOf(Collection::class)
        ->and($sections->count())->toBeGreaterThan(0)
        ->and($sections->first())->toBeInstanceOf(SectionData::class);

    Event::assertDispatched(ResponseReceived::class);
})->group('sections');
