<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\DocumentIndex\IndexDateDTO;
use CodebarAg\DocuWare\DTO\DocumentIndex\PrepareDTO;

it('can guess the index dto', function () {

    $name = 'Date';
    $value = now();

    $instance = PrepareDTO::guess($name, $value);

    expect($instance)
        ->toBeInstanceOf(IndexDateDTO::class);

})->group('dto');
