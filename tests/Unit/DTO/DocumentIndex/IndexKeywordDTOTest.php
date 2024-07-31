<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexKeywordDTO;

it('create prepare index keyword dto', function () {

    $name = 'Name';
    $value = 'Keyword Value';

    $instance = IndexKeywordDTO::make($name, $value);

    expect($instance)
        ->toBeInstanceOf(IndexKeywordDTO::class)
        ->and($instance->values())
        ->toBeArray()
        ->toMatchArray([
            'FieldName' => $name,
            'Item' => $value,
            'ItemElementName' => 'Keyword',
        ]);

})->group('dto');
