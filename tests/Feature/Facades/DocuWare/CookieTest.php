<?php

use CodebarAg\DocuWare\Facades\DocuWare;

uses()->group('docuware');

it('cookie method', function () {

    $url = config('docuware.credentials.url');
    $username = config('docuware.credentials.username');
    $password = config('docuware.credentials.password');

    $cookie = DocuWare::cookie($url, $username, $password);
    expect($cookie)->not()->toBeEmpty();
});
