<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Clip;
use Illuminate\Support\Facades\Event;

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
})->group('clip');
