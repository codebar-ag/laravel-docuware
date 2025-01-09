<?php

use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\AppendASinglePDFToADocument;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('can replace a pdf document section', function () {
    Event::fake();

    $fileCabinetId = env('DOCUWARE_TESTS_FILE_CABINET_ID');

    $document = $this->connector->send(new CreateDataRecord(
        fileCabinetId: $fileCabinetId,
        fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-1.pdf'),
        fileName: 'test-1.pdf',
        indexes: null
    ))->dto();

    $documentWithSingleAddition = $this->connector->send(new AppendASinglePDFToADocument(
        fileCabinetId: $fileCabinetId,
        documentId: $document->id,
        fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-2.pdf'),
        fileName: 'test-2.pdf',
    ))->dto();

    $response = $this->connector->send(new GetASpecificDocumentFromAFileCabinet($fileCabinetId, $document->id))->dto();

    expect($response)->toBeInstanceOf(Document::class)
        ->and($response->sections->count())->toBe(2)
        ->and($response->sections->first()->originalFileName)->toBe('test-1.pdf')
        ->and($response->sections->last()->originalFileName)->toBe('test-2.pdf');

    Event::assertDispatched(DocuWareResponseLog::class);
});
