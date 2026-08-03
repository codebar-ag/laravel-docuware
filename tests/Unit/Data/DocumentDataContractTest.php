<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Exceptions\MalformedResponseException;
use Illuminate\Support\Carbon;

it('parses document timestamps to the correct century (ms regression)', function () {
    $document = DocumentData::fromDocuWare([
        'Id' => 7,
        'CreatedAt' => '/Date(1700000000000)/',
        'LastModified' => '/Date(1700000000000)/',
    ]);

    expect($document->created_at)->toBeInstanceOf(Carbon::class)
        ->and($document->created_at->year)->toBe(2023)
        ->and($document->updated_at->year)->toBe(2023);
});

it('returns null timestamps when the API omits the date fields', function () {
    $document = DocumentData::fromDocuWare(['Id' => 7]);

    expect($document->created_at)->toBeNull()
        ->and($document->updated_at)->toBeNull()
        ->and($document->file_size)->toBe(0);
});

it('requires the identity field and names it on failure', function () {
    expect(fn () => DocumentData::fromDocuWare(['Title' => 'no id here']))
        ->toThrow(MalformedResponseException::class, 'Id');
});
