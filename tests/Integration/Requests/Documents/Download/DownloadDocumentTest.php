<?php

use CodebarAg\DocuWare\Enums\TargetFileType;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can download a document', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    $contents = DocuWare::documents($this->cabinet)->download($document->id, TargetFileType::AUTO);

    expect(strlen($contents))->toBe(strlen('::fake-file-content::'));

    Event::assertDispatched(ResponseReceived::class);
})->group('download');
