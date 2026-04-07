<?php

use CodebarAg\DocuWare\DTO\Section;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\Sections\GetAllSectionsFromADocument;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can get all sections from a document', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $sections = $this->connector->send(new GetAllSectionsFromADocument(
        $fileCabinetId,
        $document->id
    ))->dto();

    expect($sections)->toBeInstanceOf(Collection::class)
        ->and($sections->count())->toBeGreaterThan(0)
        ->and($sections->first())->toBeInstanceOf(Section::class);

    Event::assertDispatched(DocuWareResponseLog::class);

})->group('requests', 'sections');
