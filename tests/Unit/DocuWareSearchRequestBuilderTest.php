<?php

use Carbon\Carbon;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\GetDocuments;
use CodebarAg\DocuWare\Requests\Search\GetSearchRequest;

it('builds dialog expression conditions for filter empty and not empty', function () {
    $request = (new DocuWare)
        ->searchRequestBuilder()
        ->fileCabinet('cabinet-id')
        ->filterEmpty('STATUS')
        ->filterNotEmpty('OTHER_FIELD')
        ->get();

    expect($request)->toBeInstanceOf(GetSearchRequest::class);

    $condition = $request->defaultBody()['Condition'];

    expect($condition)->toContain(['DBName' => 'STATUS', 'Value' => ['EMPTY()']])
        ->and($condition)->toContain(['DBName' => 'OTHER_FIELD', 'Value' => ['NOTEMPTY()']]);

    foreach ($condition as $row) {
        expect(array_is_list($row['Value']))->toBeTrue();
    }
})->group('search', 'unit');

it('builds trash query with empty and not empty filters', function () {
    $request = (new DocuWare)
        ->searchRequestBuilder()
        ->trashBin()
        ->filterEmpty('STATUS')
        ->filterNotEmpty('OTHER_FIELD')
        ->get();

    expect($request)->toBeInstanceOf(GetDocuments::class);

    $condition = $request->defaultBody()['Condition'];

    expect($condition)->toContain(['DBName' => 'STATUS', 'Value' => ['EMPTY()']])
        ->and($condition)->toContain(['DBName' => 'OTHER_FIELD', 'Value' => ['NOTEMPTY()']]);

    foreach ($condition as $row) {
        expect(array_is_list($row['Value']))->toBeTrue();
    }
})->group('search', 'unit');

it('encodes date range filter values as a zero-indexed list for json', function () {
    $request = (new DocuWare)
        ->searchRequestBuilder()
        ->fileCabinet('cabinet-id')
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::parse('2020-01-01'))
        ->filterDate('DWSTOREDATETIME', '<', Carbon::parse('2021-01-01'))
        ->get();

    expect($request)->toBeInstanceOf(GetSearchRequest::class);

    $condition = collect($request->defaultBody()['Condition'])
        ->firstWhere('DBName', 'DWSTOREDATETIME');

    expect($condition)->not->toBeNull()
        ->and(array_is_list($condition['Value']))->toBeTrue()
        ->and($condition['Value'])->toHaveCount(2);
})->group('search', 'unit');
