<?php

use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\AppendFilesToADataRecord;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;
use Saloon\Data\MultipartValue;

it('can attach a file to a data record', function () {
    Event::fake();

    $fileCabinetId = env('DOCUWARE_TESTS_FILE_CABINET_ID');

    $document = $this->connector->send(new CreateDataRecord(
        fileCabinetId: $fileCabinetId,
        fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-1.pdf'),
        fileName: 'test-1.pdf',
        indexes: null
    ))->dto();

    $response = $this->connector->send(
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

    expect($response)->toBeInstanceOf(Document::class)
        ->and($response->sections->count())->toBe(2)
        ->and($response->sections->first()->originalFileName)->toBe('test-1.pdf')
        ->and($response->sections->last()->originalFileName)->toBe('test-2.pdf');

    Event::assertDispatched(DocuWareResponseLog::class);
});

it('can attach files to a data record', function () {
    Event::fake();

    $fileCabinetId = env('DOCUWARE_TESTS_FILE_CABINET_ID');

    $document = $this->connector->send(new CreateDataRecord(
        fileCabinetId: $fileCabinetId,
        fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-1.pdf'),
        fileName: 'test-1.pdf',
        indexes: null
    ))->dto();

    $response = $this->connector->send(
        new AppendFilesToADataRecord(
            fileCabinetId: $fileCabinetId,
            dataRecordId: $document->id,
            files: collect([
                new MultipartValue(
                    name: 'File[]',
                    value: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-2.pdf'),
                    filename: 'test-2.pdf',
                ),
                new MultipartValue(
                    name: 'File[]',
                    value: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-3.pdf'),
                    filename: 'test-3.pdf',
                ),
            ])
        )
    )->dto();

    $sections = $response->sections->values();

    ray($sections->toArray());

    expect($response)->toBeInstanceOf(Document::class)
        ->and($sections->count())->toBe(3)
        ->and($sections[0]->originalFileName)->toBe('test-1.pdf')
        ->and($sections[1]->originalFileName)->toBe('test-2.pdf')
        ->and($sections[2]->originalFileName)->toBe('test-3.pdf');

    Event::assertDispatched(DocuWareResponseLog::class);
})->skip();
