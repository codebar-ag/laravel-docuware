<?php

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Batch\BatchDocumentsUpdateFields;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('runs a batch index update by document id', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt',
        collect([IndexTextDTO::make('DOCUMENT_LABEL', 'batch-test')]),
    ))->dto();

    Sleep::for(2)->seconds();

    $payload = [
        'Source' => [
            '$type' => 'BatchUpdateDocumentsSource',
            'Id' => [(int) $document->id],
        ],
        'Data' => [
            'Field' => [
                [
                    'FieldName' => 'DOCUMENT_LABEL',
                    'Item' => 'batch-updated',
                ],
            ],
            'StoreDialogId' => '',
            'BatchSize' => '100',
            'BreakOnError' => false,
            'ForceUpdate' => true,
        ],
    ];

    $this->connector->send(new BatchDocumentsUpdateFields(
        $fileCabinetId,
        $payload,
    ))->dto();

    Event::assertDispatched(DocuWareResponseLog::class);
});
