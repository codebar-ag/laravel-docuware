<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateTimeDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDecimalDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexNumericDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTableDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use Illuminate\Support\Arr;

it('create prepare index text dto using dto', function () {
    $name = 'TABLE';

    $now = now();

    $dtoRows = collect([
        collect([
            IndexTextDTO::make('TEXT', 'project_1'),
            IndexNumericDTO::make('INT', 1),
            IndexDecimalDTO::make('DECIMAL', 1.1),
            IndexDateDTO::make('DATE', $now),
            IndexDateTimeDTO::make('DATETIME', $now),
        ]),
        collect([
            IndexTextDTO::make('TEXT', 'project_2'),
            IndexNumericDTO::make('INT', 2),
            IndexDecimalDTO::make('DECIMAL', 2.2),
            IndexDateDTO::make('DATE', $now),
            IndexDateTimeDTO::make('DATETIME', $now),
        ]),
    ]);

    $field = IndexTableDTO::make($name, $dtoRows)->values();

    expect(Arr::get($field, 'FieldName'))
        ->toBe('TABLE')
        ->and(Arr::get($field, 'ItemElementName'))
        ->toBe('Table')
        ->and(Arr::get($field, 'Item.$type'))
        ->toBe('DocumentIndexFieldTable')
        ->and(Arr::get($field, 'Item.Row'))
        ->toBeArray()
        ->and(Arr::get($field, 'Item.Row'))
        ->toBe([
            [
                'ColumnValue' => [
                    [
                        'FieldName' => 'TEXT',
                        'Item' => 'project_1',
                        'ItemElementName' => 'String',
                    ],
                    [
                        'FieldName' => 'INT',
                        'Item' => 1,
                        'ItemElementName' => 'Int',
                    ],
                    [
                        'FieldName' => 'DECIMAL',
                        'Item' => 1.1,
                        'ItemElementName' => 'Decimal',
                    ],
                    [
                        'FieldName' => 'DATE',
                        'Item' => $now->toDateString(),
                        'ItemElementName' => 'String',
                    ],
                    [
                        'FieldName' => 'DATETIME',
                        'Item' => $now->toDateTimeString(),
                        'ItemElementName' => 'String',
                    ],
                ],
            ],
            [
                'ColumnValue' => [
                    [
                        'FieldName' => 'TEXT',
                        'Item' => 'project_2',
                        'ItemElementName' => 'String',
                    ],
                    [
                        'FieldName' => 'INT',
                        'Item' => 2,
                        'ItemElementName' => 'Int',
                    ],
                    [
                        'FieldName' => 'DECIMAL',
                        'Item' => 2.2,
                        'ItemElementName' => 'Decimal',
                    ],
                    [
                        'FieldName' => 'DATE',
                        'Item' => $now->toDateString(),
                        'ItemElementName' => 'String',
                    ],
                    [
                        'FieldName' => 'DATETIME',
                        'Item' => $now->toDateTimeString(),
                        'ItemElementName' => 'String',
                    ],
                ],
            ],
        ]);
})->group('dto');
