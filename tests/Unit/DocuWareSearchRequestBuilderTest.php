<?php

use Carbon\Carbon;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\GetDocuments;
use CodebarAg\DocuWare\Requests\Search\GetSearchRequest;

/**
 * @return list<mixed>
 */
function dateConditionValues(string $operator, Carbon $date): array
{
    $request = (new DocuWare)
        ->searchRequestBuilder()
        ->fileCabinet('cabinet-id')
        ->filterDate('DWSTOREDATETIME', $operator, $date)
        ->get();

    return collect($request->defaultBody()['Condition'])
        ->firstWhere('DBName', 'DWSTOREDATETIME')['Value'];
}

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

it('replaces a date bound in place when the same operator is used again', function () {
    $request = (new DocuWare)
        ->searchRequestBuilder()
        ->fileCabinet('cabinet-id')
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::parse('2020-01-01'))
        ->filterDate('DWSTOREDATETIME', '<', Carbon::parse('2021-01-01'))
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::parse('2020-06-01'))
        ->get();

    $condition = collect($request->defaultBody()['Condition'])
        ->firstWhere('DBName', 'DWSTOREDATETIME');

    $start = Carbon::parse('2020-06-01')->startOfDay();
    $end = Carbon::parse('2021-01-01')->startOfDay();

    expect($condition)->not->toBeNull()
        ->and($condition['Value'])->toHaveCount(2)
        ->and($condition['Value'][0])->toBeInstanceOf(Carbon::class)
        ->and($condition['Value'][0]->equalTo($start))->toBeTrue()
        ->and($condition['Value'][1]->equalTo($end))->toBeTrue();
})->group('search', 'unit');

it('snaps the lower bound of a date filter to the start of day', function (string $operator) {
    $values = dateConditionValues($operator, Carbon::parse('2020-06-15 13:45:30'));

    expect($values[0])->toBeInstanceOf(Carbon::class)
        ->and($values[0]->equalTo(Carbon::parse('2020-06-15')->startOfDay()))->toBeTrue();
})->with(['>=', '<'])->group('search', 'unit');

it('snaps the upper bound of a date filter to the end of day', function (string $operator) {
    $values = dateConditionValues($operator, Carbon::parse('2020-06-15 13:45:30'));

    expect($values[0])->toBeInstanceOf(Carbon::class)
        ->and($values[0]->equalTo(Carbon::parse('2020-06-15')->endOfDay()))->toBeTrue();
})->with(['>', '<='])->group('search', 'unit');

it('leaves an exact-match (=) date filter value untouched', function () {
    $values = dateConditionValues('=', Carbon::parse('2020-06-15 13:45:30'));

    expect($values[0])->toBeInstanceOf(Carbon::class)
        ->and($values[0]->equalTo(Carbon::parse('2020-06-15 13:45:30')))->toBeTrue();
})->group('search', 'unit');

it('fills a single lower-bound date filter with now() as the synthetic upper bound', function () {
    Carbon::setTestNow('2025-01-02 03:04:05');

    $values = dateConditionValues('>=', Carbon::parse('2020-06-15'));

    expect($values)->toHaveCount(2)
        ->and($values[1])->toBeInstanceOf(Carbon::class)
        ->and($values[1]->equalTo(Carbon::parse('2025-01-02 03:04:05')))->toBeTrue();

    Carbon::setTestNow();
})->group('search', 'unit');

it('fills a single upper-bound date filter with the epoch as the synthetic lower bound', function () {
    $values = dateConditionValues('<', Carbon::parse('2020-06-15'));

    expect($values)->toHaveCount(2)
        ->and($values[1])->toBeInstanceOf(Carbon::class)
        ->and($values[1]->getTimestamp())->toBe(0);
})->group('search', 'unit');

it('throws when a two-bound date range diverges (lower bound after upper bound)', function () {
    (new DocuWare)
        ->searchRequestBuilder()
        ->fileCabinet('cabinet-id')
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::parse('2021-01-01'))
        ->filterDate('DWSTOREDATETIME', '<', Carbon::parse('2020-01-01'))
        ->get();
})->throws(UnableToSearch::class)->group('search', 'unit');

it('throws when more than two date bounds are added to the same field', function () {
    (new DocuWare)
        ->searchRequestBuilder()
        ->fileCabinet('cabinet-id')
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::parse('2020-01-01'))
        ->filterDate('DWSTOREDATETIME', '<', Carbon::parse('2021-01-01'))
        ->filterDate('DWSTOREDATETIME', '>', Carbon::parse('2020-06-01'));
})->throws(UnableToSearch::class)->group('search', 'unit');
