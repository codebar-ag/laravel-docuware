<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Data\Documents\DocumentFieldData;
use CodebarAg\DocuWare\Data\Write\IndexFields;
use CodebarAg\DocuWare\Enums\TargetFileType;
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadDocument;
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\DeleteDocument;
use CodebarAg\DocuWare\Requests\Documents\UpdateIndexValues\UpdateIndexValues;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Carbon;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    config()->set('laravel-docuware.default', 'default');
    config()->set('laravel-docuware.instances.default', [
        'grant' => 'credentials', 'url' => 'https://example.docuware.cloud',
        'username' => 'alice', 'password' => 'secret',
    ]);
    config()->set('laravel-docuware.configurations.cache.driver', 'array');
    config()->set('laravel-docuware.configurations.cache.lifetime_in_seconds', 60);
    config()->set('laravel-docuware.configurations.request.timeout_in_seconds', 30);
    config()->set('laravel-docuware.configurations.client_id', 'x');
    config()->set('laravel-docuware.configurations.scope', 'y');
});

it('builds typed index entries with the correct wire format', function () {
    $fields = IndexFields::make()
        ->text('STATUS', 'open')
        ->number('AMOUNT', 42)
        ->date('DUE', Carbon::parse('2026-01-01'));

    $entries = $fields->toCollection();

    expect($entries)->toHaveCount(3)
        ->and($entries->first()->values())->toMatchArray([
            'FieldName' => 'STATUS',
            'Item' => 'open',
            'ItemElementName' => 'String',
        ])
        ->and($entries->get(1)->values()['ItemElementName'])->toBe('Int');
});

it('stores a document and returns DocumentData', function () {
    $client = clientWithMock([
        CreateDataRecord::class => MockResponse::make(fakeDocumentPayload(7)),
    ]);

    $document = $client->documents('cab-1')->store(
        fileContent: '::content::',
        fileName: 'invoice.pdf',
        indexes: IndexFields::make()->text('STATUS', 'open'),
    );

    expect($document)->toBeInstanceOf(DocumentData::class)->and($document->id)->toBe(7);
});

it('updates index values and returns the updated fields', function () {
    $client = clientWithMock([
        UpdateIndexValues::class => MockResponse::make([
            'Field' => [
                ['FieldName' => 'STATUS', 'ItemElementName' => 'String', 'Item' => 'closed', 'IsNull' => false, 'SystemField' => false, 'FieldLabel' => 'Status'],
            ],
        ]),
    ]);

    $fields = $client->documents('cab-1')->update(7, IndexFields::make()->text('STATUS', 'closed'));

    expect($fields)->toHaveCount(1)
        ->and($fields->get('STATUS'))->toBeInstanceOf(DocumentFieldData::class)
        ->and($fields->get('STATUS')->value)->toBe('closed');
});

it('deletes a document', function () {
    $client = clientWithMock([
        DeleteDocument::class => MockResponse::make([], 200),
    ]);

    $client->documents('cab-1')->delete(7);
})->throwsNoExceptions();

it('downloads document content', function () {
    $client = clientWithMock([
        DownloadDocument::class => MockResponse::make('::binary-pdf::', 200),
    ]);

    $content = $client->documents('cab-1')->download(7, TargetFileType::PDF);

    expect($content)->toBe('::binary-pdf::');
});
