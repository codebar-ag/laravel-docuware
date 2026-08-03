<?php

use CodebarAg\DocuWare\Data\Support\Field;
use CodebarAg\DocuWare\Exceptions\MalformedResponseException;

it('extracts and coerces a required string', function () {
    expect(Field::string(['Id' => 'abc'], 'Id'))->toBe('abc')
        ->and(Field::string(['Id' => 5], 'Id'))->toBe('5');
});

it('throws a clear exception for a missing required string', function () {
    expect(fn () => Field::string([], 'Id', 'RoleData'))
        ->toThrow(MalformedResponseException::class, 'Id');
});

it('extracts and coerces a required int', function () {
    expect(Field::int(['n' => '7'], 'n'))->toBe(7);
    expect(fn () => Field::int([], 'n'))->toThrow(MalformedResponseException::class);
});

it('returns null for optional scalars rather than throwing', function () {
    expect(Field::stringOrNull([], 'x'))->toBeNull()
        ->and(Field::stringOrNull(['x' => 9], 'x'))->toBe('9')
        ->and(Field::intOrNull([], 'x'))->toBeNull()
        ->and(Field::intOrNull(['x' => '4'], 'x'))->toBe(4);
});

it('coerces booleans, tolerating the XML string form', function () {
    expect(Field::bool(['b' => 'true'], 'b'))->toBeTrue()
        ->and(Field::bool(['b' => 'false'], 'b'))->toBeFalse()
        ->and(Field::bool(['b' => true], 'b'))->toBeTrue()
        ->and(Field::bool([], 'b'))->toBeFalse()
        ->and(Field::bool([], 'b', true))->toBeTrue()
        ->and(Field::boolOrNull([], 'b'))->toBeNull()
        ->and(Field::boolOrNull(['b' => 'false'], 'b'))->toBeFalse();
});
