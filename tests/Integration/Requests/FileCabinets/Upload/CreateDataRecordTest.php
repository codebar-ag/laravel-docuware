<?php

use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\DTO\Documents\DocumentField;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;

it('can upload a data record without a file using a discovered text field', function () {
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $textField = sandboxFieldName($this->connector, 'Text');

    $document = recordFixture(
        new CreateDataRecord(
            $fileCabinetId,
            null,
            null,
            collect([IndexTextDTO::make($textField, '::data-entry::')]),
        ),
        'file-cabinets/upload/create-data-record-without-file',
    )->dto();

    expect($document)->toBeInstanceOf(Document::class)
        ->and($document->id)->toBeInt();

    $field = $document->fields[$textField];

    expect($field)->toBeInstanceOf(DocumentField::class)
        ->and($field->name)->toBe($textField)
        ->and($field->type)->toBe('String')
        ->and($field->value)->toBe('::data-entry::');
})->group('live');

it('can upload a data record with file content using a discovered text field', function () {
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $textField = sandboxFieldName($this->connector, 'Text');

    $document = recordFixture(
        new CreateDataRecord(
            $fileCabinetId,
            '::fake-file-content::',
            'example.txt',
            collect([IndexTextDTO::make($textField, '::text::')]),
        ),
        'file-cabinets/upload/create-data-record-with-file',
    )->dto();

    expect($document)->toBeInstanceOf(Document::class)
        ->and($document->title)->toBe('example');

    $field = $document->fields[$textField];

    expect($field->name)->toBe($textField)
        ->and($field->value)->toBe('::text::');
})->group('live');
