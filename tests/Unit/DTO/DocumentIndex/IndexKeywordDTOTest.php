<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexKeywordDTO;

it('create prepare index keyword dto', function () {

    $name = 'Name';
    $values = ['Keyword Value'];

    $instance = IndexKeywordDTO::make($name, $values);

    expect($instance)
        ->toBeInstanceOf(IndexKeywordDTO::class)
        ->and($instance->values())
        ->toBeArray()
        ->toMatchArray([
            'FieldName' => $name,
            'Keywords' => $values,
            'ItemElementName' => 'Keywords',
        ]);

})->group('dto');

it('serializes multiple keyword values as a plural array', function () {

    $instance = IndexKeywordDTO::make('ORDER_NUMBERS', ['S387230', '2610726', '4144']);

    expect($instance->values())->toBe([
        'FieldName' => 'ORDER_NUMBERS',
        'Keywords' => ['S387230', '2610726', '4144'],
        'ItemElementName' => 'Keywords',
    ]);

})->group('dto');

it('serializes an empty keyword list as an empty array', function () {

    $instance = IndexKeywordDTO::make('ORDER_NUMBERS', []);

    expect($instance->values())->toBe([
        'FieldName' => 'ORDER_NUMBERS',
        'Keywords' => [],
        'ItemElementName' => 'Keywords',
    ]);

})->group('dto');
