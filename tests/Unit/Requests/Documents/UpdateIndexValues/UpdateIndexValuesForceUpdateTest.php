<?php

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\Requests\Documents\UpdateIndexValues\UpdateIndexValues;

it('emits ForceUpdate=true when requested', function () {
    $request = new UpdateIndexValues(
        fileCabinetId: 'cab-1',
        documentId: '42',
        indexes: collect([IndexTextDTO::make('DOCUMENT_TYPE', 'Invoice')]),
        forceUpdate: true,
    );

    expect($request->defaultBody()['ForceUpdate'])->toBeTrue();
})->group('unit');

it('emits ForceUpdate=false by default', function () {
    $request = new UpdateIndexValues(
        fileCabinetId: 'cab-1',
        documentId: '42',
        indexes: collect([IndexTextDTO::make('DOCUMENT_TYPE', 'Invoice')]),
    );

    expect($request->defaultBody()['ForceUpdate'])->toBeFalse();
})->group('unit');
