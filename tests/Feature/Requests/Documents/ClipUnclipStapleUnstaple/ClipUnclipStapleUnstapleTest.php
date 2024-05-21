<?php

use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Clip;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Staple;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Unclip;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Unstaple;
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\DeleteDocument;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetDocumentsFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

function cleanup($connector, $fileCabinetId): void
{
    $paginator = $connector->send(new GetDocumentsFromAFileCabinet(
        $fileCabinetId
    ))->dto();

    foreach ($paginator->documents as $document) {
        $connector->send(new DeleteDocument(
            $fileCabinetId,
            $document->id,
        ))->dto();
    }
}

function uploadFiles($connector, $fileCabinetId, $path): array
{
    $document = $connector->send(new CreateDataRecord(
        $fileCabinetId,
        file_get_contents($path.'/test-1.pdf'),
        'test-1.pdf',
    ))->dto();

    $document2 = $connector->send(new CreateDataRecord(
        $fileCabinetId,
        file_get_contents($path.'/test-2.pdf'),
        'test-2.pdf',
    ))->dto();

    sleep(5); // Wait for the files to be uploaded and processed

    // Have to get document again as returned data is incorrect
    $document = $connector->send(new GetASpecificDocumentFromAFileCabinet(
        $fileCabinetId,
        $document->id
    ))->dto();

    // Have to get document2 again as returned data is incorrect
    $document2 = $connector->send(new GetASpecificDocumentFromAFileCabinet(
        $fileCabinetId,
        $document2->id
    ))->dto();

    return [$document, $document2];
}

it('can clip 2 documents', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.basket_id');
    $path = __DIR__.'/../../../../Fixtures/files';

    cleanup($this->connector, $fileCabinetId);

    [$document, $document2] = uploadFiles($this->connector, $fileCabinetId, $path);

    $clip = $this->connector->send(new Clip(
        $fileCabinetId,
        [
            $document->id,
            $document2->id,
        ]
    ))->dto();

    expect($clip->id)->toBe($document->id)
        ->and($clip->total_pages)->toBe($document->total_pages + $document2->total_pages)
        ->and($clip->sections->count())->toBe(2);

    Event::assertDispatched(DocuWareResponseLog::class);

    return [$clip, $document, $document2];
});

it('can unclip 2 documents', function ($test) {
    Event::fake();

    [$clip, $document, $document2] = $test;

    $fileCabinetId = config('laravel-docuware.tests.basket_id');

    sleep(5);

    $unclip = $this->connector->send(new Unclip(
        $fileCabinetId,
        $clip->id
    ))->dto();

    expect($unclip->documents->count())->toBe(2)
        ->and($unclip->documents->first()->title)->toBe($document->title)
        ->and($unclip->documents->first()->file_size)->toBe($document->file_size)
        ->and($unclip->documents->last()->title)->toBe($document2->title)
        ->and($unclip->documents->last()->file_size)->toBe($document2->file_size);

    Event::assertDispatched(DocuWareResponseLog::class);
})->depends('it can clip 2 documents');

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

    expect($staple->title)->toBe($document->title)
        ->and($staple->total_pages)->toBe($document->total_pages + $document2->total_pages);

    Event::assertDispatched(DocuWareResponseLog::class);

    return $staple;

});

it('can unstaple 2 documents', function ($staple) {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.basket_id');

    sleep(5);

    $unclip = $this->connector->send(new Unstaple(
        $fileCabinetId,
        $staple->id
    ))->dto();

    expect($unclip->documents->count())->toBe($staple->total_pages);

    Event::assertDispatched(DocuWareResponseLog::class);
})->depends('it can staple 2 documents');
