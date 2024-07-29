<?php

use Illuminate\Support\Sleep;

test('sleep', function () {

    Log::info(now()->toDateTimeString());
    Sleep::for(5)->seconds();
    Log::info(now()->toDateTimeString());

})->expectNotToPerformAssertions()
    ->skip('Testing sleep function.');
