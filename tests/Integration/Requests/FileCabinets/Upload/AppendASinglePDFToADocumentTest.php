<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can append a single pdf to a document', function () {
    Event::fake();

    $document = DocuWare::documents($this->cabinet)->store(
        fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-1.pdf'),
        fileName: 'test-1.pdf',
    );

    DocuWare::documents($this->cabinet)->appendPdf(
        $document->id,
        file_get_contents(__DIR__.'/../../../../Fixtures/files/test-2.pdf'),
        'test-2.pdf',
    );

    $response = DocuWare::documents($this->cabinet)->find($document->id);

    expect($response)->toBeInstanceOf(DocumentData::class)
        ->and($response->sections->count())->toBe(2)
        ->and($response->sections->first()->originalFileName)->toBe('test-1.pdf')
        ->and($response->sections->last()->originalFileName)->toBe('test-2.pdf');

    Event::assertDispatched(ResponseReceived::class);
});
