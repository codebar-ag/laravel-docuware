<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\DocumentIndex\IndexDateTimeDTO;

it('create index date time dto', function () {

    $name = 'Date';
    $value = now();

    $instance = IndexDateTimeDTO::make($name, $value);

    expect($instance)
        ->toBeInstanceOf(IndexDateTimeDTO::class)
        ->and($instance->values())
        ->toBeArray()
        ->toMatchArray([
            'FieldName' => $name,
            'Item' => $value->toDateTimeString(),
            'ItemElementName' => 'String',
        ]);

})->group('dto');
