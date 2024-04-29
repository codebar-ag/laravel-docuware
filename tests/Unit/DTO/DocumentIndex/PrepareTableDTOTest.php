<?php

namespace CodebarAg\DocuWare\Tests\Unit\DTO;

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\PrepareTableDTO;

it('create prepare index table dto', function () {

    $name = 'Date';
    $value = now();

    $instance = PrepareTableDTO::make($name, $value);

    expect($instance)
        ->toBeInstanceOf(IndexDateDTO::class);

})->group('dto');
