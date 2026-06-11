<?php

use CodebarAg\DocuWare\Facades\DocuWare;

it('can authenticate and perform a call through the facade', function () {
    expect(DocuWare::organizations()->all())->not->toBeEmpty();
})->group('integration', 'authentication');

it('throws an error if credentials are wrong')
    ->skip('OAuth flow is internal in 2.0')
    ->group('integration', 'authentication');
