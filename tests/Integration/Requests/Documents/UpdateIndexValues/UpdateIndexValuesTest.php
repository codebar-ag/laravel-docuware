<?php

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexNumericDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\Requests\Documents\UpdateIndexValues\UpdateIndexValues;
use Illuminate\Support\Collection;

it('can update a single document index value', function () {
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $textField = sandboxFieldName($this->connector, 'Text');

    $document = uploadTestDocument($this->connector);

    $response = recordFixture(
        new UpdateIndexValues($fileCabinetId, $document->id, collect([
            IndexTextDTO::make($textField, 'laravel-docuware'),
        ])),
        'documents/update-index-values/single',
    )->dto();

    expect($response)->toBeInstanceOf(Collection::class)
        ->and($response[$textField])->toBe('laravel-docuware');
})->group('live');

it('can update multiple document index values of different types', function () {
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $textField = sandboxFieldName($this->connector, 'Text');
    $numericField = sandboxFieldName($this->connector, 'Numeric');

    $document = uploadTestDocument($this->connector);

    $response = recordFixture(
        new UpdateIndexValues($fileCabinetId, $document->id, collect([
            IndexTextDTO::make($textField, 'laravel-docuware'),
            IndexNumericDTO::make($numericField, 42),
        ]), true),
        'documents/update-index-values/multiple',
    )->dto();

    expect($response)->toBeInstanceOf(Collection::class)
        ->and($response[$textField])->toBe('laravel-docuware')
        ->and($response)->toHaveKey($numericField);
})->group('live');
