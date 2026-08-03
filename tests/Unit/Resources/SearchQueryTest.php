<?php

use CodebarAg\DocuWare\DocuWareManager;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;
use CodebarAg\DocuWare\Resources\Search\SearchQuery;
use Illuminate\Support\Carbon;

beforeEach(function () {
    config()->set('laravel-docuware.default', 'default');
    config()->set('laravel-docuware.instances.default', [
        'grant' => 'credentials', 'url' => 'https://example.docuware.cloud',
        'username' => 'alice', 'password' => 'secret',
    ]);
    config()->set('laravel-docuware.configurations.cache.driver', 'array');
});

function newQuery(?string $cabinet = 'cab-1'): SearchQuery
{
    return new SearchQuery(app(DocuWareManager::class)->instance(), $cabinet);
}

/** Invoke the private buildCondition() to inspect the exact wire shape. */
function builtCondition(SearchQuery $query): array
{
    $method = new ReflectionMethod($query, 'buildCondition');
    $method->setAccessible(true);

    return collect($method->invoke($query))->keyBy('DBName')->all();
}

it('builds an empty condition when no filters or full-text are set', function () {
    expect(builtCondition(newQuery()))->toBe([]);
});

it('emits a DocuWareFulltext condition for full-text search', function () {
    $condition = builtCondition(newQuery()->fullText('invoice'));

    expect($condition['DocuWareFulltext']['Value'])->toBe(['invoice']);
});

it('quotes string values and passes scalars through untouched', function () {
    $condition = builtCondition(newQuery()->where('STATUS', 'open')->where('AMOUNT', 42));

    expect($condition['STATUS']['Value'])->toBe(['"open"'])
        ->and($condition['AMOUNT']['Value'])->toBe([42]);
});

it('joins whereIn values with OR and quotes each string', function () {
    $condition = builtCondition(newQuery()->whereIn('TYPE', ['a', 'b', 'c']));

    expect($condition['TYPE']['Value'])->toBe(['"a" OR "b" OR "c"']);
});

it('adds two bounds for a date-between filter', function () {
    $condition = builtCondition(
        newQuery()->whereDateBetween('DWSTOREDATETIME', Carbon::parse('2024-01-01'), Carbon::parse('2024-12-31'))
    );

    expect($condition['DWSTOREDATETIME']['Value'])->toHaveCount(2);
});

it('guards against an unset cabinet, non-positive page and perPage', function () {
    expect(fn () => newQuery(null)->get())->toThrow(UnableToSearch::class);
    expect(fn () => newQuery()->page(0)->get())->toThrow(UnableToSearch::class);
    expect(fn () => newQuery()->perPage(0)->get())->toThrow(UnableToSearch::class);
});

it('records ordering via latest() and oldest()', function () {
    $latest = newQuery()->latest('AMOUNT');
    $oldest = newQuery()->oldest();

    $read = fn (SearchQuery $q, string $p) => (function () use ($p) {
        return $this->{$p};
    })->call($q);

    expect($read($latest, 'orderField'))->toBe('AMOUNT')
        ->and($read($latest, 'orderDirection'))->toBe('desc')
        ->and($read($oldest, 'orderDirection'))->toBe('asc');
});
