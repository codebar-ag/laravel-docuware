<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\AddApplicationProperties;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('adds application properties to a document', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $added = $this->connector->send(new AddApplicationProperties(
        $fileCabinetId,
        $document->id,
        [
            ['Name' => 'Key1', 'Value' => 'Key1 Value'],
            ['Name' => 'Key2', 'Value' => 'Key2 Value'],
        ],
    ))->dto();

    expect($added)->toBeInstanceOf(Collection::class)
        ->and($added->count())->toBe(2)
        ->and($added->first()['Name'])->toBe('Key1');

    Event::assertDispatched(DocuWareResponseLog::class);
});
