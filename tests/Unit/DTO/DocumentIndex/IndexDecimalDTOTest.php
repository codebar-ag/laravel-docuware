<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDecimalDTO;

it('create index numeric dto', function () {

    $name = 'Numeric';
    $value = 100.00;

    $instance = IndexDecimalDTO::make($name, $value);

    expect($instance)
        ->toBeInstanceOf(IndexDecimalDTO::class)
        ->and($instance->values())
        ->toBeArray()
        ->toMatchArray([
            'FieldName' => $name,
            'Item' => $value,
            'ItemElementName' => 'Decimal',
        ]);

})->group('dto');

it('keeps a null decimal value as null instead of coercing it to 0.0', function () {

    $instance = IndexDecimalDTO::make('SKONTOBETRAG', null);

    expect($instance->values())->toBe([
        'FieldName' => 'SKONTOBETRAG',
        'Item' => null,
        'ItemElementName' => 'Decimal',
    ]);

})->group('dto');
