<?php

use CodebarAg\DocuWare\Facades\DocuWare;

uses()->group('docuware');

it('cookie method', function () {
    $cookie = DocuWare::cookie();
    expect($cookie)->not()->toBeEmpty();
});
