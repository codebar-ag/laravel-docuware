<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\PrepareDTO;

it('create prepare makeContent dto', function () {
    $indexes = collect([
        IndexDateDTO::make('Date', now()),
    ]);

    expect(PrepareDTO::makeFields($indexes))
        ->toBeArray();

})->group('dto');
