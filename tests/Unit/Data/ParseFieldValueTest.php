<?php

use CodebarAg\DocuWare\Data\Documents\TableRowData;
use CodebarAg\DocuWare\Data\Support\ParseFieldValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

it('returns null for a null field or an explicit null value', function () {
    expect(ParseFieldValue::field(null))->toBeNull()
        ->and(ParseFieldValue::field(['IsNull' => true, 'Item' => 'x', 'ItemElementName' => 'String']))->toBeNull();
});

it('coerces each scalar item type to its native PHP type', function () {
    expect(ParseFieldValue::field(['ItemElementName' => 'Int', 'Item' => '42']))->toBe(42)
        ->and(ParseFieldValue::field(['ItemElementName' => 'Decimal', 'Item' => '1.5']))->toBe(1.5)
        ->and(ParseFieldValue::field(['ItemElementName' => 'String', 'Item' => 5]))->toBe('5');
});

it('parses date/datetime items and tolerates a non-string item', function () {
    expect(ParseFieldValue::field(['ItemElementName' => 'Date', 'Item' => '/Date(1700000000000)/']))
        ->toBeInstanceOf(Carbon::class)
        ->and(ParseFieldValue::field(['ItemElementName' => 'DateTime', 'Item' => null]))->toBeNull();
});

it('joins keyword arrays and degrades safely when shape differs', function () {
    expect(ParseFieldValue::field(['ItemElementName' => 'Keywords', 'Item' => ['Keyword' => ['a', 'b']]]))
        ->toBe('a, b')
        ->and(ParseFieldValue::field(['ItemElementName' => 'Keywords', 'Item' => 'flat']))->toBe('');
});

it('parses a table item into a collection of TableRowData', function () {
    $value = ParseFieldValue::field([
        'ItemElementName' => 'Table',
        'Item' => [
            '$type' => 'DocumentIndexFieldTable',
            'Row' => [
                ['ColumnValue' => [['FieldName' => 'A', 'Item' => 'x', 'ItemElementName' => 'String']]],
                'not-a-row',
            ],
        ],
    ]);

    expect($value)->toBeInstanceOf(Collection::class)
        ->and($value)->toHaveCount(1)
        ->and($value->first())->toBeInstanceOf(TableRowData::class)
        ->and($value->first()->fields['A']->value)->toBe('x');
});

it('returns null for a malformed table or unknown item type', function () {
    expect(ParseFieldValue::field(['ItemElementName' => 'Table', 'Item' => 'nope']))->toBeNull()
        ->and(ParseFieldValue::field(['ItemElementName' => 'Table', 'Item' => ['$type' => 'wrong']]))->toBeNull()
        ->and(ParseFieldValue::field(['ItemElementName' => 'Mystery', 'Item' => 'x']))->toBeNull();
});
