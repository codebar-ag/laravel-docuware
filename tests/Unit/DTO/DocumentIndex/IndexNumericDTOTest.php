<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\DocumentIndex\IndexNumericDTO;

it('create index numeric dto', function () {

    $name = 'Numeric';
    $value = 100;

    $instance = IndexNumericDTO::make($name, $value);

    expect($instance)
        ->toBeInstanceOf(IndexNumericDTO::class)
        ->and($instance->values())
        ->toBeArray()
        ->toMatchArray([
            'FieldName' => $name,
            'Item' => $value,
            'ItemElementName' => 'Int',
        ]);

})->group('dto');
