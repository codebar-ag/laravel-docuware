<?php

use CodebarAg\DocuWare\Data\Documents\DocumentFieldData;
use CodebarAg\DocuWare\Data\Write\IndexFields;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;

it('can update a single document index value', function () {
    $textField = sandboxFieldName($this->cabinet, 'Text');

    $document = uploadTestDocument($this->cabinet);

    $fields = DocuWare::documents($this->cabinet)->update(
        $document->id,
        IndexFields::make()->text($textField, 'laravel-docuware'),
    );

    expect($fields)->toBeInstanceOf(Collection::class)
        ->and($fields->get($textField))->toBeInstanceOf(DocumentFieldData::class)
        ->and($fields->get($textField)->value)->toBe('laravel-docuware');
});

it('can update multiple document index values of different types', function () {
    $textField = sandboxFieldName($this->cabinet, 'Text');
    $numericField = sandboxFieldName($this->cabinet, 'Numeric');

    $document = uploadTestDocument($this->cabinet);

    $fields = DocuWare::documents($this->cabinet)->update(
        $document->id,
        IndexFields::make()
            ->text($textField, 'laravel-docuware')
            ->number($numericField, 42),
        forceUpdate: true,
    );

    expect($fields)->toBeInstanceOf(Collection::class)
        ->and($fields->get($textField)->value)->toBe('laravel-docuware')
        ->and($fields)->toHaveKey($numericField);
});
