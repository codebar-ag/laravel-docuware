<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Data\Documents\DocumentFieldData;
use CodebarAg\DocuWare\Data\Write\IndexFields;
use CodebarAg\DocuWare\Facades\DocuWare;

it('can upload a data record without a file using a discovered text field', function () {
    $textField = sandboxFieldName($this->cabinet, 'Text');

    $document = DocuWare::documents($this->cabinet)->store(
        fileContent: null,
        fileName: null,
        indexes: IndexFields::make()->text($textField, '::data-entry::'),
    );

    expect($document)->toBeInstanceOf(DocumentData::class)
        ->and($document->id)->toBeInt();

    $field = $document->fields[$textField];

    expect($field)->toBeInstanceOf(DocumentFieldData::class)
        ->and($field->name)->toBe($textField)
        ->and($field->value)->toBe('::data-entry::');
});

it('can upload a data record with file content using a discovered text field', function () {
    $textField = sandboxFieldName($this->cabinet, 'Text');

    $document = DocuWare::documents($this->cabinet)->store(
        fileContent: '::fake-file-content::',
        fileName: 'example.txt',
        indexes: IndexFields::make()->text($textField, '::text::'),
    );

    expect($document)->toBeInstanceOf(DocumentData::class)
        ->and($document->title)->toBe('example');

    $field = $document->fields[$textField];

    expect($field->name)->toBe($textField)
        ->and($field->value)->toBe('::text::');
});
