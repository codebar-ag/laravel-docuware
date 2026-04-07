<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Staple;
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

    $expectedMinPages = $document->total_pages + $document2->total_pages;

    expect($staple->title)->toBe($document->title)
        ->and($staple->total_pages)->toBeGreaterThanOrEqual($expectedMinPages)
        ->and($staple->sections->count())->toBeGreaterThanOrEqual(1);

    Event::assertDispatched(DocuWareResponseLog::class);
})->group('staple');
