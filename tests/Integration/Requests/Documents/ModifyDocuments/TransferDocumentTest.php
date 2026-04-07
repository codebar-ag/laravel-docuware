<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\TransferDocument;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('transfers a document to another file cabinet or basket', function () {
    Event::fake();

    $sourceCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $destinationId = config('laravel-docuware.tests.basket_id');
    $storeDialogId = config('laravel-docuware.tests.store_dialog_id')
        ?? config('laravel-docuware.tests.dialog_id');

    $document = $this->connector->send(new CreateDataRecord(
        $sourceCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    Sleep::for(2)->seconds();

    $ok = $this->connector->send(new TransferDocument(
        fileCabinetId: $sourceCabinetId,
        destinationFileCabinetId: $destinationId,
        documentId: (string) $document->id,
        storeDialogId: $storeDialogId ? (string) $storeDialogId : null,
    ))->dto();

    expect($ok)->toBeTrue();

    Event::assertDispatched(DocuWareResponseLog::class);
})->skip('Requires a valid destination cabinet/basket and store dialog for your tenant.');
