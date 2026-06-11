<?php

use CodebarAg\DocuWare\Data\Write\IndexFields;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('runs a batch index update by document id', function () {
    Event::fake();

    $textField = sandboxFieldName($this->cabinet, 'Text');

    $document = DocuWare::documents($this->cabinet)->store(
        fileContent: '::fake-file-content::',
        fileName: 'example.txt',
        indexes: IndexFields::make()->text($textField, 'batch-test'),
    );

    Sleep::for(2)->seconds();

    $payload = [
        'Source' => [
            '$type' => 'BatchUpdateDocumentsSource',
            'Id' => [(int) $document->id],
        ],
        'Data' => [
            'Field' => [
                [
                    'FieldName' => $textField,
                    'Item' => 'batch-updated',
                ],
            ],
            'StoreDialogId' => '',
            'BatchSize' => '100',
            'BreakOnError' => false,
            'ForceUpdate' => true,
        ],
    ];

    DocuWare::documents($this->cabinet)->batchUpdate($payload);

    Event::assertDispatched(ResponseReceived::class);
});
