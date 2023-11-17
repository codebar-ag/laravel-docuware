<?php

use CodebarAg\DocuWare\Facades\DocuWare;

uses()->group('docuware');

it('cookie method', function () {

    $url = config('laravel-docuware.credentials.url');
    $username = config('laravel-docuware.credentials.username');
    $password = config('laravel-docuware.credentials.password');

    $cookie = DocuWare::cookie($url, $username, $password);
    expect($cookie)->not()->toBeEmpty();
});
