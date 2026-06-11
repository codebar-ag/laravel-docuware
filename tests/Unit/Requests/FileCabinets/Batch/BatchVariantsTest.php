<?php

use CodebarAg\DocuWare\Requests\FileCabinets\Batch\BatchDocumentsUpdateFields;

it('builds the by-id batch with the update-process content type', function () {
    $request = BatchDocumentsUpdateFields::byId(
        fileCabinetId: 'cab-1',
        ids: [309, 310],
        fields: [['FieldName' => 'DOCUMENT_TYPE', 'Item' => 'Batch Update Test']],
    );

    $body = $request->body()->all();

    expect($request->defaultHeaders())->toBe(['Content-Type' => BatchDocumentsUpdateFields::CONTENT_TYPE_UPDATE])
        ->and($body['Source']['$type'])->toBe('BatchUpdateDocumentsSource')
        ->and($body['Source']['Id'])->toBe([309, 310])
        ->and($body['Data']['Field'][0]['FieldName'])->toBe('DOCUMENT_TYPE')
        ->and($body['Data']['BatchSize'])->toBe('100')
        ->and($body['Data']['ForceUpdate'])->toBeTrue();
})->group('unit');

it('builds the by-search batch with a dialog expression source', function () {
    $request = BatchDocumentsUpdateFields::bySearch(
        fileCabinetId: 'cab-1',
        expression: ['Operation' => 'And', 'Condition' => [['DBName' => 'DOCUMENT_TYPE', 'Value' => ['Test']]]],
        fields: [['FieldName' => 'DOCUMENT_TYPE', 'Item' => 'Batch Update Test']],
    );

    $body = $request->body()->all();

    expect($request->defaultHeaders())->toBe(['Content-Type' => BatchDocumentsUpdateFields::CONTENT_TYPE_UPDATE])
        ->and($body['Source']['$type'])->toBe('BatchUpdateDialogExpressionSource')
        ->and($body['Source']['Expression']['Operation'])->toBe('And');
})->group('unit');

it('builds the keyword append batch with the keyword content type', function () {
    $request = BatchDocumentsUpdateFields::appendKeywords(
        fileCabinetId: 'cab-1',
        docIds: [309, 310],
        keywords: ['Value1', 'Value2'],
        fieldName: 'ORDER_NUMBER',
    );

    $body = $request->body()->all();

    expect($request->defaultHeaders())->toBe(['Content-Type' => BatchDocumentsUpdateFields::CONTENT_TYPE_KEYWORD])
        ->and($body['DocId'])->toBe([309, 310])
        ->and($body['Keyword'])->toBe(['Value1', 'Value2'])
        ->and($body['FieldName'])->toBe('ORDER_NUMBER');
})->group('unit');

it('sends no custom content type for a raw payload', function () {
    $request = new BatchDocumentsUpdateFields('cab-1', ['Source' => []]);

    expect($request->defaultHeaders())->toBe([]);
})->group('unit');
