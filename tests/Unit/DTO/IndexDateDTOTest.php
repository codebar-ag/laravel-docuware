<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\DocumentIndex\IndexDateDTO;

it('create index date dto', function () {

    $name = 'Date';
    $value = now();

    $instance = IndexDateDTO::make($name, $value);

    expect($instance)
        ->toBeInstanceOf(IndexDateDTO::class)
        ->and($instance->values())
        ->toBeArray()
        ->toMatchArray([
            'FieldName' => $name,
            'Item' => $value->toDateString(),
            'ItemElementName' => 'String',
        ]);

})->group('dto');
