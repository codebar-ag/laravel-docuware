<?php

use CodebarAg\DocuWare\Enums\TargetFileType;
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadDocument;

it('defaults to Auto without annotations', function () {
    $request = new DownloadDocument('cab-1', '42');

    expect($request->defaultQuery())->toBe([
        'targetFileType' => 'Auto',
        'keepAnnotations' => 'false',
    ]);
})->group('unit');

it('reflects the chosen target file type and keepAnnotations flag', function () {
    $request = new DownloadDocument('cab-1', '42', TargetFileType::PDF, true);

    expect($request->defaultQuery())->toBe([
        'targetFileType' => 'PDF',
        'keepAnnotations' => 'true',
    ]);
})->group('unit');

it('supports the Original target file type', function () {
    $request = new DownloadDocument('cab-1', '42', TargetFileType::ORIGINAL);

    expect($request->defaultQuery()['targetFileType'])->toBe('Original');
})->group('unit');
