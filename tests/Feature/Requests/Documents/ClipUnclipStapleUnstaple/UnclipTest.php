<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Clip;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Unclip;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can unclip 2 documents', function () {
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

    Sleep::for(5)->seconds();

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
})->group('unclip');
