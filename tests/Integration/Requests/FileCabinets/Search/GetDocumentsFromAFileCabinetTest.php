<?php

use CodebarAg\DocuWare\Data\Documents\DocumentPageData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can get all documents', function () {
    Event::fake();

    uploadTestDocument($this->cabinet);
    uploadTestDocument($this->cabinet);

    Sleep::for(2)->seconds(); // Wait for the documents to be processed

    $page = DocuWare::documents($this->cabinet)->search()->get();

    expect($page)->toBeInstanceOf(DocumentPageData::class)
        ->and($page->total)->toBeGreaterThanOrEqual(2)
        ->and($page->documents->count())->toBeGreaterThanOrEqual(2);

    Event::assertDispatched(ResponseReceived::class);
});
