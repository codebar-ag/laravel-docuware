<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can preview a document image', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    $image = DocuWare::documents($this->cabinet)->preview((string) $document->id);

    expect(strlen($image))->toBeGreaterThan(0);

    Event::assertDispatched(ResponseReceived::class);
});
