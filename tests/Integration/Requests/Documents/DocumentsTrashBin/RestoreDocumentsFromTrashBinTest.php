<?php

use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\RestoreDocuments;
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\DeleteDocument;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('can restore documents in trash', function () {
    Event::fake();

    $document = $this->connector->send(new CreateDataRecord(
        config('laravel-docuware.tests.file_cabinet_id'),
        file_get_contents(__DIR__.'/../../../../Fixtures/files/test-1.pdf'),
        'test-1.pdf',
    ))->dto();

    $this->connector->send(new DeleteDocument(
        config('laravel-docuware.tests.file_cabinet_id'),
        $document->id,
    ))->dto();

    $paginatorRequest = (new DocuWare)
        ->searchRequestBuilder()
        ->trashBin()
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $delete = $this->connector->send(new RestoreDocuments($paginator->mappedDocuments->pluck('ID')->all()))->dto();

    expect($delete->successCount)->toBe($paginator->total);
})->group('restore', 'trash');
