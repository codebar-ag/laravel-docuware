<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\DocumentField;
use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\DTO\Field;
use CodebarAg\DocuWare\DTO\FileCabinet;
use CodebarAg\DocuWare\DTO\Organization;
use CodebarAg\DocuWare\DTO\OrganizationIndex;
use CodebarAg\DocuWare\DTO\SuggestionField;
use CodebarAg\DocuWare\DTO\TableRow;

uses()->group('dto');

it('create a fake organization', function () {
    $fake = Organization::fake();
    $this->assertInstanceOf(Organization::class, $fake);
});

it('create a fake organization index', function () {
    $fake = OrganizationIndex::fake();
    $this->assertInstanceOf(OrganizationIndex::class, $fake);
});

it('create a fake file cabinet', function () {
    $fake = FileCabinet::fake();
    $this->assertInstanceOf(FileCabinet::class, $fake);
});

it('create a fake dialog', function () {
    $fake = Dialog::fake();

    $this->assertInstanceOf(Dialog::class, $fake);
});

it('create a fake field', function () {
    $fake = Field::fake();

    $this->assertInstanceOf(Field::class, $fake);
});

it('create a fake document field', function () {
    $fake = DocumentField::fake();

    $this->assertInstanceOf(DocumentField::class, $fake);
});

it('create a fake suggestion field', function () {
    $fake = SuggestionField::fake();

    $this->assertInstanceOf(SuggestionField::class, $fake);
});

it('create a fake document', function () {
    $fake = Document::fake();

    $this->assertInstanceOf(Document::class, $fake);
});

it('create a fake document paginator', function () {
    $fake = DocumentPaginator::fake();

    $this->assertInstanceOf(DocumentPaginator::class, $fake);
});

it('create a fake table row', function () {
    $fake = TableRow::fake();

    $this->assertInstanceOf(TableRow::class, $fake);
});
