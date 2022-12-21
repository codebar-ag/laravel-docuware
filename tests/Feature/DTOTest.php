<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\DocumentField;
use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\DTO\Field;
use CodebarAg\DocuWare\DTO\FileCabinet;
use CodebarAg\DocuWare\DTO\TableRow;

it(' create a fake file cabinet', function () {
    $fake = FileCabinet::fake();
    $this->assertInstanceOf(FileCabinet::class, $fake);
})->group('dto');

it('create a fake dialog', function () {
    $fake = Dialog::fake();

    $this->assertInstanceOf(Dialog::class, $fake);
})->group('dto');

it(' create a fake field', function () {
    $fake = Field::fake();

    $this->assertInstanceOf(Field::class, $fake);
})->group('dto');

it('create a fake document field', function () {
    $fake = DocumentField::fake();

    $this->assertInstanceOf(DocumentField::class, $fake);
})->group('dto');

it('create a fake document', function () {
    $fake = Document::fake();

    $this->assertInstanceOf(Document::class, $fake);
})->group('dto');

it('create a fake document paginator', function () {
    $fake = DocumentPaginator::fake();

    $this->assertInstanceOf(DocumentPaginator::class, $fake);
})->group('dto');

it('create a fake table row', function () {
    $fake = TableRow::fake();

    $this->assertInstanceOf(TableRow::class, $fake);
})->group('dto');
