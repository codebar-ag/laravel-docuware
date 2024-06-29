<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;

it('create prepare index text dto', function () {

    $name = 'Name';
    $value = 'String Value';

    $instance = IndexTextDTO::make($name, $value);

    expect($instance)
        ->toBeInstanceOf(IndexTextDTO::class)
        ->and($instance->values())
        ->toBeArray()
        ->toMatchArray([
            'FieldName' => $name,
            'Item' => $value,
            'ItemElementName' => 'String',
        ]);

})->group('dto');
