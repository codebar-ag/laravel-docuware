<?php

use CodebarAg\DocuWare\DTO\Documents\Annotations\AnnotationBuilder;
use CodebarAg\DocuWare\DTO\Documents\Annotations\Location;
use CodebarAg\DocuWare\DTO\Documents\Annotations\TextEntry;
use CodebarAg\DocuWare\Requests\Documents\Stamps\AddDocumentAnnotations;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Sleep;

it('posts a text annotation to a document via the typed builder', function () {
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    // Annotations target rendered pages, so upload a real PDF.
    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        file_get_contents(__DIR__.'/../../../../Fixtures/files/test-1.pdf'),
        'test-1.pdf',
    ))->dto();

    Sleep::for(3)->seconds();

    $payload = AnnotationBuilder::make()
        ->addEntry(new TextEntry('laravel-docuware', Location::make(100, 100, 1500, 500)))
        ->toArray();

    $response = recordFixture(
        new AddDocumentAnnotations($fileCabinetId, $document->id, $payload),
        'documents/stamps/add-document-annotations',
    );

    expect($response->successful())->toBeTrue('HTTP '.$response->status().': '.$response->body());
})->group('live');
