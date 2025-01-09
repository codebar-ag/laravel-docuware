<?php

use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\AppendFilesToADataRecord;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\ReplaceAPDFDocumentSection;
use Illuminate\Support\Facades\Event;
use Saloon\Data\MultipartValue;

it('can replace a pdf document section', function () {
    Event::fake();

    $fileCabinetId = env('DOCUWARE_TESTS_FILE_CABINET_ID');

    $document = $this->connector->send(new CreateDataRecord(
        fileCabinetId: $fileCabinetId,
        fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-1.pdf'),
        fileName: 'test-1.pdf',
        indexes: null
    ))->dto();

    $documentWithSections = $this->connector->send(
        new AppendFilesToADataRecord(
            fileCabinetId: $fileCabinetId,
            dataRecordId: $document->id,
            files: collect([
                new MultipartValue(
                    name: 'File[]',
                    value: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-2.pdf'),
                    filename: 'test-2.pdf',
                ),
            ])
        )
    )->dto();

    expect($documentWithSections)->toBeInstanceOf(Document::class)
        ->and($documentWithSections->sections->count())->toBe(2)
        ->and($documentWithSections->sections->first()->originalFileName)->toBe('test-1.pdf')
        ->and($documentWithSections->sections->last()->originalFileName)->toBe('test-2.pdf');

    $documentWithSectionReplaced = $this->connector->send(new ReplaceAPDFDocumentSection(
        fileCabinetId: $fileCabinetId,
        sectionId: $documentWithSections->sections->last()->id,
        fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-3.pdf'),
        fileName: 'test-3.pdf',
    ))->dto();

    $response = $this->connector->send(new GetASpecificDocumentFromAFileCabinet($fileCabinetId, Str::before($documentWithSectionReplaced->id, '-')))->dto();

    expect($response)->toBeInstanceOf(Document::class)
        ->and($response->sections->count())->toBe(2)
        ->and($response->sections->first()->originalFileName)->toBe('test-1.pdf')
        ->and($response->sections->last()->originalFileName)->toBe('test-3.pdf');

    Event::assertDispatched(DocuWareResponseLog::class);
})->only();
