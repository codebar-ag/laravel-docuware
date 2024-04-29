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
