<?php

use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;

it('resolves upload endpoint path without embedding store dialog query', function () {
    $request = new CreateDataRecord('cab-1', null, null, null, 'store-dialog-id');

    expect($request->resolveEndpoint())->toBe('/FileCabinets/cab-1/Documents')
        ->and($request->defaultQuery())->toBe(['StoreDialogId' => 'store-dialog-id']);
})->group('unit');

it('omits store dialog id from default query when null', function () {
    $request = new CreateDataRecord('cab-1', null, null, null, null);

    expect($request->defaultQuery())->toBe([]);
})->group('unit');

it('omits store dialog id from default query when empty string', function () {
    $request = new CreateDataRecord('cab-1', null, null, null, '');

    expect($request->defaultQuery())->toBe([]);
})->group('unit');
