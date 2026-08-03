<?php

use CodebarAg\DocuWare\Facades\DocuWare;

it('can perform an authenticated call through the facade', function () {
    expect(DocuWare::organizations()->all())->not->toBeEmpty();
})->group('integration', 'authentication');
