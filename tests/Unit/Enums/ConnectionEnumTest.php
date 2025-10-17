<?php

use CodebarAg\DocuWare\Enums\ConnectionEnum;

it('has all expected connection enum cases', function () {
    $expectedCases = [
        'WITHOUT_COOKIE',
        'STATIC_COOKIE',
        'DYNAMIC_COOKIE',
    ];

    $actualCases = array_map(fn ($case) => $case->value, ConnectionEnum::cases());

    expect($actualCases)->toBe($expectedCases);
});

it('can get string value from enum', function () {
    expect(ConnectionEnum::WITHOUT_COOKIE->value)->toBe('WITHOUT_COOKIE');
    expect(ConnectionEnum::STATIC_COOKIE->value)->toBe('STATIC_COOKIE');
    expect(ConnectionEnum::DYNAMIC_COOKIE->value)->toBe('DYNAMIC_COOKIE');
});

it('can create enum from string value', function () {
    expect(ConnectionEnum::from('WITHOUT_COOKIE'))->toBe(ConnectionEnum::WITHOUT_COOKIE);
    expect(ConnectionEnum::from('STATIC_COOKIE'))->toBe(ConnectionEnum::STATIC_COOKIE);
    expect(ConnectionEnum::from('DYNAMIC_COOKIE'))->toBe(ConnectionEnum::DYNAMIC_COOKIE);
});

it('throws exception for invalid enum value', function () {
    expect(fn () => ConnectionEnum::from('INVALID_VALUE'))
        ->toThrow(ValueError::class);
});

it('can try to create enum from string value', function () {
    expect(ConnectionEnum::tryFrom('WITHOUT_COOKIE'))->toBe(ConnectionEnum::WITHOUT_COOKIE);
    expect(ConnectionEnum::tryFrom('INVALID_VALUE'))->toBeNull();
});
