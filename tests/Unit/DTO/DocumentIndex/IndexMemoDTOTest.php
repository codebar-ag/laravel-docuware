<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexMemoDTO;

it('create prepare index memo dto', function () {

    $name = 'Name';
    $value = 'Memo Value';

    $instance = IndexMemoDTO::make($name, $value);

    expect($instance)
        ->toBeInstanceOf(IndexMemoDTO::class)
        ->and($instance->values())
        ->toBeArray()
        ->toMatchArray([
            'FieldName' => $name,
            'Item' => $value,
            'ItemElementName' => 'Memo',
        ]);

})->group('dto');
