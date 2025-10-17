<?php

use CodebarAg\DocuWare\Enums\DialogType;

it('has all expected dialog type enum cases', function () {
    $expectedCases = [
        'Search',
        'Store',
        'Result',
        'Index',
        'List',
        'Folders',
    ];

    $actualCases = array_map(fn ($case) => $case->value, DialogType::cases());

    expect($actualCases)->toBe($expectedCases);
});

it('can get string value from enum', function () {
    expect(DialogType::SEARCH->value)->toBe('Search');
    expect(DialogType::STORE->value)->toBe('Store');
    expect(DialogType::RESULT->value)->toBe('Result');
    expect(DialogType::INDEX->value)->toBe('Index');
    expect(DialogType::LIST->value)->toBe('List');
    expect(DialogType::FOLDERS->value)->toBe('Folders');
});

it('can create enum from string value', function () {
    expect(DialogType::from('Search'))->toBe(DialogType::SEARCH);
    expect(DialogType::from('Store'))->toBe(DialogType::STORE);
    expect(DialogType::from('Result'))->toBe(DialogType::RESULT);
    expect(DialogType::from('Index'))->toBe(DialogType::INDEX);
    expect(DialogType::from('List'))->toBe(DialogType::LIST);
    expect(DialogType::from('Folders'))->toBe(DialogType::FOLDERS);
});

it('throws exception for invalid enum value', function () {
    expect(fn () => DialogType::from('InvalidType'))
        ->toThrow(ValueError::class);
});

it('can try to create enum from string value', function () {
    expect(DialogType::tryFrom('Search'))->toBe(DialogType::SEARCH);
    expect(DialogType::tryFrom('InvalidType'))->toBeNull();
});
