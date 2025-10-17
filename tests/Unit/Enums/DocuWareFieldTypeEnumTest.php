<?php

use CodebarAg\DocuWare\Enums\DocuWareFieldTypeEnum;

it('has all expected field type enum cases', function () {
    $expectedCases = [
        'String',
        'Int',
        'Decimal',
        'Date',
        'DateTime',
        'Table',
    ];

    $actualCases = array_map(fn ($case) => $case->value, DocuWareFieldTypeEnum::cases());

    expect($actualCases)->toBe($expectedCases);
});

it('can get string value from enum', function () {
    expect(DocuWareFieldTypeEnum::STRING->value)->toBe('String');
    expect(DocuWareFieldTypeEnum::INT->value)->toBe('Int');
    expect(DocuWareFieldTypeEnum::DECIMAL->value)->toBe('Decimal');
    expect(DocuWareFieldTypeEnum::DATE->value)->toBe('Date');
    expect(DocuWareFieldTypeEnum::DATETIME->value)->toBe('DateTime');
    expect(DocuWareFieldTypeEnum::TABLE->value)->toBe('Table');
});

it('can create enum from string value', function () {
    expect(DocuWareFieldTypeEnum::from('String'))->toBe(DocuWareFieldTypeEnum::STRING);
    expect(DocuWareFieldTypeEnum::from('Int'))->toBe(DocuWareFieldTypeEnum::INT);
    expect(DocuWareFieldTypeEnum::from('Decimal'))->toBe(DocuWareFieldTypeEnum::DECIMAL);
    expect(DocuWareFieldTypeEnum::from('Date'))->toBe(DocuWareFieldTypeEnum::DATE);
    expect(DocuWareFieldTypeEnum::from('DateTime'))->toBe(DocuWareFieldTypeEnum::DATETIME);
    expect(DocuWareFieldTypeEnum::from('Table'))->toBe(DocuWareFieldTypeEnum::TABLE);
});

it('throws exception for invalid enum value', function () {
    expect(fn () => DocuWareFieldTypeEnum::from('InvalidType'))
        ->toThrow(ValueError::class);
});

it('can try to create enum from string value', function () {
    expect(DocuWareFieldTypeEnum::tryFrom('String'))->toBe(DocuWareFieldTypeEnum::STRING);
    expect(DocuWareFieldTypeEnum::tryFrom('InvalidType'))->toBeNull();
});

it('can be used in switch statements', function () {
    $fieldType = DocuWareFieldTypeEnum::STRING;

    $result = match ($fieldType) {
        DocuWareFieldTypeEnum::STRING => 'text field',
        DocuWareFieldTypeEnum::INT => 'numeric field',
        DocuWareFieldTypeEnum::DECIMAL => 'decimal field',
        DocuWareFieldTypeEnum::DATE => 'date field',
        DocuWareFieldTypeEnum::DATETIME => 'datetime field',
        DocuWareFieldTypeEnum::TABLE => 'table field',
    };

    expect($result)->toBe('text field');
});
