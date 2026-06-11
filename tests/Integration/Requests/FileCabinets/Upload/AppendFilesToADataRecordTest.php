<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;
use Saloon\Data\MultipartValue;

it('can attach a file to a data record', function () {
    Event::fake();

    $document = DocuWare::documents($this->cabinet)->store(
        fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-1.pdf'),
        fileName: 'test-1.pdf',
    );

    $response = DocuWare::documents($this->cabinet)->appendFiles(
        $document->id,
        collect([
            new MultipartValue(
                name: 'File[]',
                value: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-2.pdf'),
                filename: 'test-2.pdf',
            ),
        ]),
    );

    expect($response)->toBeInstanceOf(DocumentData::class)
        ->and($response->sections->count())->toBe(2)
        ->and($response->sections->first()->originalFileName)->toBe('test-1.pdf')
        ->and($response->sections->last()->originalFileName)->toBe('test-2.pdf');

    Event::assertDispatched(ResponseReceived::class);
});

it('can attach files to a data record', function () {
    Event::fake();

    $document = DocuWare::documents($this->cabinet)->store(
        fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-1.pdf'),
        fileName: 'test-1.pdf',
    );

    $response = DocuWare::documents($this->cabinet)->appendFiles(
        $document->id,
        collect([
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
        ]),
    );

    $sections = $response->sections->values();

    expect($response)->toBeInstanceOf(DocumentData::class)
        ->and($sections->count())->toBe(3)
        ->and($sections[0]->originalFileName)->toBe('test-1.pdf')
        ->and($sections[1]->originalFileName)->toBe('test-2.pdf')
        ->and($sections[2]->originalFileName)->toBe('test-3.pdf');

    Event::assertDispatched(ResponseReceived::class);
});
