<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Staple;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;
use Illuminate\Support\Facades\Event;

it('can staple 2 documents', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.basket_id');
    $path = __DIR__.'/../../../../Fixtures/files';

    cleanup($this->connector, $fileCabinetId);

    [$document, $document2] = uploadFiles($this->connector, $fileCabinetId, $path);

    $staple = $this->connector->send(new Staple(
        $fileCabinetId,
        [
            $document->id,
            $document2->id,
        ]
    ))->dto();

    sleep(5); // Wait for the files to be uploaded and processed

    $stapledDocument = $this->connector->send(new GetASpecificDocumentFromAFileCabinet(
        $fileCabinetId,
        $staple->id
    ))->dto();

    expect($stapledDocument->title)->toBe($document->title)
        ->and($stapledDocument->total_pages)->toBe($document->total_pages + $document2->total_pages);

    Event::assertDispatched(DocuWareResponseLog::class);
})->group('staple');
