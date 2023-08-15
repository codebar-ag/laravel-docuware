<?php

use CodebarAg\DocuWare\DocuWareConnector;
use CodebarAg\DocuWare\Support\Auth;

it('returns the correct default config', function () {
    $connector = new DocuWareConnector();

    expect($connector->defaultConfig())->toBe([
        'timeout' => config('docuware.timeout'),
        'cookies', Auth::cookieJar(),
    ]);
})->group('connector')->todo();

it('returns the correct base url', function () {
    $connector = new DocuWareConnector();

    expect($connector->resolveBaseUrl())->toBe(config('docuware.credentials.url').'/DocuWare/Platform');
})->group('connector');

it('returns the correct default headers', function () {
    $connector = new DocuWareConnector();

    expect($connector->defaultHeaders())->toBe([
        'Accept' => 'application/json',
    ]);
})->group('connector');
