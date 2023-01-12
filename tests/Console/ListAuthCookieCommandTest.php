<?php

namespace CodebarAg\DocuWare\Tests\Console;

use CodebarAg\DocuWare\Support\Auth;
use Illuminate\Support\Arr;

uses()->group('console');

it('lists auth cookie without creation date', function () {
    $this->artisan('docuware:list-auth-cookie')
        ->assertSuccessful()
        ->expectsOutputToContain(Arr::get(Auth::cookies(), Auth::COOKIE_NAME))
        ->doesntExpectOutputToContain('created at:');
});

it('lists auth cookie with creation date', function () {
    $this->artisan('docuware:list-auth-cookie --with-date')
        ->assertSuccessful()
        ->expectsOutputToContain(Arr::get(Auth::cookies(), Auth::COOKIE_NAME))
        ->expectsOutputToContain('created at:');
});
