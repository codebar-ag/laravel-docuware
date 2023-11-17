<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use CodebarAg\DocuWare\DTO\DocumentIndex\IndexText;

uses()->group('dto');

it('create a fake organization', function () {

    $name = 'Name';
    $value = 'String Value';

    $instance = IndexText::make($name, $value);

    expect($instance)
        ->toBeInstanceOf(IndexText::class)
        ->and($instance->values())
        ->toBeArray()
        ->toMatchArray([
            'FieldName' => $name,
            'Item' => $value,
            'ItemElementName' => 'String',
        ]);

});
