<?php

use CodebarAg\DocuWare\Facades\DocuWare;

it('authenticates with credentials and performs a call', function () {
    expect(DocuWare::organizations()->all())->not->toBeEmpty();
})->group('integration', 'authentication');
