<?php

use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\Support\Auth;

it('it does automatically login a user', function () {
    $this->assertNull(Auth::cookies());

    (new DocuWare())->getFileCabinets();

    $this->assertArrayHasKey(Auth::COOKIE_NAME, Auth::cookies());

    (new DocuWare())->logout();

    $this->assertNull(Auth::cookies());

})->group('cookies');
