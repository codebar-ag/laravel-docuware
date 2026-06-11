<?php

use CodebarAg\DocuWare\Support\JsonArrays;

it('returns an empty list for non-array input', function () {
    expect(JsonArrays::listOfRecords('not-an-array'))->toBe([])
        ->and(JsonArrays::listOfRecords(null))->toBe([])
        ->and(JsonArrays::associativeRow('x'))->toBe([]);
});

it('normalizes a list of records and skips non-array items', function () {
    expect(JsonArrays::listOfRecords([['a' => 1], 'skip', ['b' => 2], 5]))
        ->toBe([['a' => 1], ['b' => 2]]);
});

it('takes the values of a map of records (DocuWare sometimes keys rows)', function () {
    expect(JsonArrays::listOfRecords(['first' => ['a' => 1], 'second' => ['b' => 2]]))
        ->toBe([['a' => 1], ['b' => 2]]);
});

it('stringifies keys on an associative row', function () {
    expect(JsonArrays::associativeRow([0 => 'a', 1 => 'b']))
        ->toBe(['0' => 'a', '1' => 'b']);
});
