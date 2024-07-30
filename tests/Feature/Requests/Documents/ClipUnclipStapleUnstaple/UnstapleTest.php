<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Staple;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Unstaple;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can unstaple a document', function () {
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

    Sleep::for(5)->seconds();

    $unclip = $this->connector->send(new Unstaple(
        $fileCabinetId,
        $staple->id
    ))->dto();

    expect($unclip->documents->count())->toBe($staple->total_pages);

    Event::assertDispatched(DocuWareResponseLog::class);
})->group('unstaple');
