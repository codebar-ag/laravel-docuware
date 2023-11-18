<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\DocumentIndex\IndexDateDTO;
use CodebarAg\DocuWare\DTO\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\DTO\DocumentIndex\PrepareDTO;

it('create prepare index dto', function () {

    expect(PrepareDTO::guess('String', 'Text'))->toBeInstanceOf(IndexTextDTO::class);
    expect(PrepareDTO::guess('Numeric', 'Text'))->toBeInstanceOf(IndexTextDTO::class);
    expect(PrepareDTO::guess('Date', now()))->toBeInstanceOf(IndexDateDTO::class);

})->group('dto');

it('create prepare makeContent dto', function () {

    $name = 'Date';
    $value = now();

    $indexes = collect([PrepareDTO::guess($name, $value)]);

    expect(PrepareDTO::makeContent($indexes))
        ->toBeJson();

})->group('dto');
