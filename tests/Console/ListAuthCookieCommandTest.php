<?php

namespace CodebarAg\DocuWare\Tests\Console;

use CodebarAg\DocuWare\Support\Auth;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Arr;

uses()->group('console');

beforeEach(function () {
    Auth::forget();
//    EnsureValidCookie::check();

    if (! Arr::get(Auth::cookies(), Auth::COOKIE_NAME)){
        $this->markTestIncomplete('No cookie configured');
    }
});

it('lists auth cookie without creation date', function () {
    $this->artisan('docuware:list-auth-cookie')
        ->assertSuccessful()
        ->expectsOutputToContain(Arr::get(Auth::cookies(), Auth::COOKIE_NAME))
        ->doesntExpectOutputToContain('Created At:');
});

it('lists auth cookie with creation date', function () {
    $this->artisan('docuware:list-auth-cookie', ['--with-date' => true])
        ->assertSuccessful()
        ->expectsOutputToContain(Arr::get(Auth::cookies(), Auth::COOKIE_NAME))
        ->expectsOutputToContain('Created At:');
});
