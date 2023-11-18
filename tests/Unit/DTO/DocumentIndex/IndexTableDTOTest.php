<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\DocumentIndex\IndexTableDTO;

it('create prepare index text dto', function () {

    $name = 'TABLE';
    $rows = collect([
        0 => [
            [
                'NAME_WRONG' => 'TABLE_ID',
                'VALUE_WRONG' => '1',
            ],
            [
                'NAME_WRONG' => 'TABLE_DECIMALE',
                'VALUE_WRONG' => 1.00,
            ],
        ],
        1 => [
            [
                'NAME' => 'TABLE_ID',
                'VALUE' => '2',
            ],
            [
                'NAME' => 'TABLE_DECIMALE',
                'VALUE' => 2.00,
            ],
        ],
        2 => [
            [
                'NAME' => 'TABLE_ID',
                'VALUE' => '3',
            ],
            [
                'NAME_WRONG' => 'TABLE_DECIMALE',
                'VALUE_WRONG' => 3.00,
            ],
        ],
    ]);

    $instance = IndexTableDTO::make($name, $rows);

    expect($instance)
        ->toBeInstanceOf(IndexTableDTO::class);

    ray($instance->values());
    expect($instance->values())
        ->toBeArray()
        ->toMatchArray([
            'FieldName' => 'TABLE',
            'Item' => [
                '$type' => 'DocumentIndexFieldTable',
                'Row' => [
                    [
                        'ColumnValue' => [
                            [
                                'FieldName' => 'TABLE_ID',
                                'Item' => '2',
                                'ItemElementName' => 'String',
                            ],
                            [
                                'FieldName' => 'TABLE_DECIMALE',
                                'Item' => (float) 2.0,
                                'ItemElementName' => 'Decimal',
                            ],
                        ],
                    ],
                    [
                        'ColumnValue' => [
                            [
                                'FieldName' => 'TABLE_ID',
                                'Item' => '3',
                                'ItemElementName' => 'String',
                            ],
                        ],
                    ],
                ],
            ],
            'ItemElementName' => 'Table',
        ]);

})->group('dto', 'table');
