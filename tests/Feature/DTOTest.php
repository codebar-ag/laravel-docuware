<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\DTO\Documents\DocumentField;
use CodebarAg\DocuWare\DTO\Documents\DocumentPaginator;
use CodebarAg\DocuWare\DTO\Documents\Field;
use CodebarAg\DocuWare\DTO\Documents\TableRow;
use CodebarAg\DocuWare\DTO\FileCabinets\Dialog;
use CodebarAg\DocuWare\DTO\FileCabinets\General\FileCabinetInformation;
use CodebarAg\DocuWare\DTO\General\Organization\Organization;
use CodebarAg\DocuWare\DTO\General\Organization\OrganizationIndex;
use CodebarAg\DocuWare\DTO\SuggestionField;

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
    $fake = FileCabinetInformation::fake();
    $this->assertInstanceOf(FileCabinetInformation::class, $fake);
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
