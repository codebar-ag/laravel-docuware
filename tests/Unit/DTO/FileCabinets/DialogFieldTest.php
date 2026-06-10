<?php

use CodebarAg\DocuWare\DTO\FileCabinets\Dialog;
use CodebarAg\DocuWare\DTO\FileCabinets\DialogField;

it('parses raw dialog fields into typed DialogField objects', function () {
    $dialog = Dialog::fromJson([
        'Id' => 'dlg-1',
        'Type' => 'Search',
        'DisplayName' => 'Default',
        'IsDefault' => true,
        'FileCabinetId' => 'cab-1',
        'Fields' => [
            [
                'DBFieldName' => 'DOCUMENT_TYPE',
                'DlgLabel' => 'Document Type',
                'DWFieldType' => 'Text',
                'Length' => 50,
                'Precision' => 0,
                'NotEmpty' => true,
                'Visible' => true,
            ],
        ],
    ]);

    $fields = $dialog->fieldObjects();

    expect($fields)->toHaveCount(1)
        ->and($fields->first())->toBeInstanceOf(DialogField::class)
        ->and($fields->first()->dbName)->toBe('DOCUMENT_TYPE')
        ->and($fields->first()->label)->toBe('Document Type')
        ->and($fields->first()->type)->toBe('Text')
        ->and($fields->first()->length)->toBe(50)
        ->and($fields->first()->notEmpty)->toBeTrue();
})->group('unit');

it('returns an empty collection when a dialog has no fields', function () {
    $dialog = Dialog::fromJson([
        'Id' => 'dlg-1',
        'Type' => 'Store',
        'DisplayName' => 'Default',
        'IsDefault' => false,
        'FileCabinetId' => 'cab-1',
    ]);

    expect($dialog->fieldObjects())->toBeEmpty();
})->group('unit');
