<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;
use Saloon\Data\MultipartValue;

it('can replace a pdf document section', function () {
    Event::fake();

    $document = DocuWare::documents($this->cabinet)->store(
        fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-1.pdf'),
        fileName: 'test-1.pdf',
    );

    $documentWithSections = DocuWare::documents($this->cabinet)->appendFiles(
        $document->id,
        collect([
            new MultipartValue(
                name: 'File[]',
                value: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-2.pdf'),
                filename: 'test-2.pdf',
            ),
        ]),
    );

    expect($documentWithSections)->toBeInstanceOf(DocumentData::class)
        ->and($documentWithSections->sections->count())->toBe(2)
        ->and($documentWithSections->sections->first()->originalFileName)->toBe('test-1.pdf')
        ->and($documentWithSections->sections->last()->originalFileName)->toBe('test-2.pdf');

    DocuWare::documents($this->cabinet)->replaceSection(
        $documentWithSections->sections->last()->id,
        file_get_contents(__DIR__.'/../../../../Fixtures/files/test-3.pdf'),
        'test-3.pdf',
    );

    $response = DocuWare::documents($this->cabinet)->find($document->id);

    expect($response)->toBeInstanceOf(DocumentData::class)
        ->and($response->sections->count())->toBe(2)
        ->and($response->sections->first()->originalFileName)->toBe('test-1.pdf')
        ->and($response->sections->last()->originalFileName)->toBe('test-3.pdf');

    Event::assertDispatched(ResponseReceived::class);
});
