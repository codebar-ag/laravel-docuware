<?php

use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Carbon;

it('parses a millisecond /Date(ms)/ timestamp', function () {
    expect(ParseValue::date('/Date(1700000000000)/')->year)->toBe(2023);
});

it('parses a second /Date(seconds)/ timestamp', function () {
    expect(ParseValue::dateInSeconds('/Date(1700000000)/')->year)->toBe(2023);
});

it('keeps the millisecond and second parsers distinct (regression for the ms/seconds bug)', function () {
    // The SAME numeric string read as seconds lands ~1000x further in the future. This is why
    // section/workflow dates (seconds) must NOT be parsed with the millisecond parser, and v.v.
    expect(ParseValue::date('/Date(1700000000000)/')->year)->toBe(2023)
        ->and(ParseValue::dateInSeconds('/Date(1700000000000)/')->year)->toBeGreaterThan(50000);
});

it('returns null for missing/empty dates instead of throwing', function () {
    expect(ParseValue::dateOrNull(null))->toBeNull()
        ->and(ParseValue::dateOrNull(''))->toBeNull()
        ->and(ParseValue::dateInSecondsOrNull(null))->toBeNull()
        ->and(ParseValue::dateInSecondsOrNull(''))->toBeNull();
});

it('still parses present values through the null-safe variants', function () {
    expect(ParseValue::dateOrNull('/Date(0)/'))->toBeInstanceOf(Carbon::class)
        ->and(ParseValue::dateOrNull('/Date(0)/')->year)->toBe(1970);
});
