<?php

use CodebarAg\DocuWare\DTO\Documents\Annotations\AnnotationBuilder;
use CodebarAg\DocuWare\DTO\Documents\Annotations\Location;
use CodebarAg\DocuWare\DTO\Documents\Annotations\TextEntry;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Sleep;

it('posts a text annotation to a document via the typed builder', function () {
    // Annotations target rendered pages, so upload a real PDF.
    $document = DocuWare::documents($this->cabinet)->store(
        fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-1.pdf'),
        fileName: 'test-1.pdf',
    );

    Sleep::for(3)->seconds();

    $payload = AnnotationBuilder::make()
        ->addEntry(new TextEntry('laravel-docuware', Location::make(100, 100, 1500, 500)))
        ->toArray();

    $response = DocuWare::documents($this->cabinet)->annotate($document->id, $payload);

    expect($response)->not->toBeNull();
})->group('live');
