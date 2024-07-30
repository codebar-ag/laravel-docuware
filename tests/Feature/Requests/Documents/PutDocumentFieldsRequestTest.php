<?php

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\UpdateIndexValues\UpdateIndexValues;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can update a document value', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $newValue = 'laravel-docuware';

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $response = $this->connector->send(new UpdateIndexValues(
        $fileCabinetId,
        $document->id,
        collect([
            IndexTextDTO::make('UUID', $newValue),
        ])
    ))->dto();

    $this->assertSame('laravel-docuware', $response['UUID']);
    Event::assertDispatched(DocuWareResponseLog::class);

});

it('can update multiple document values', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $response = $this->connector->send(new UpdateIndexValues(
        $fileCabinetId,
        $document->id,
        collect([
            IndexTextDTO::make('UUID', 'laravel-docuware'),
            IndexTextDTO::make('DOCUMENT_LABEL', 'laravel-docuware-2'),
        ]),
        true
    ))->dto();

    $this->assertInstanceOf(Collection::class, $response);

    $this->assertSame('laravel-docuware', $response['UUID']);
    $this->assertSame('laravel-docuware-2', $response['DOCUMENT_LABEL']);

    Event::assertDispatched(DocuWareResponseLog::class);

});
