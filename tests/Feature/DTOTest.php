<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\DocumentField;
use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\DTO\Field;
use CodebarAg\DocuWare\DTO\FileCabinet;
use CodebarAg\DocuWare\Tests\TestCase;

class DTOTest extends TestCase
{
    /** @test */
    public function it_does_create_a_fake_file_cabinet()
    {
        $fake = FileCabinet::fake();

        $this->assertInstanceOf(FileCabinet::class, $fake);
    }

    /** @test */
    public function it_does_create_a_fake_dialog()
    {
        $fake = Dialog::fake();

        $this->assertInstanceOf(Dialog::class, $fake);
    }

    /** @test */
    public function it_does_create_a_fake_field()
    {
        $fake = Field::fake();

        $this->assertInstanceOf(Field::class, $fake);
    }

    /** @test */
    public function it_does_create_a_fake_document_field()
    {
        $fake = DocumentField::fake();

        $this->assertInstanceOf(DocumentField::class, $fake);
    }

    /** @test */
    public function it_does_create_a_fake_document()
    {
        $fake = Document::fake();

        $this->assertInstanceOf(Document::class, $fake);
    }

    /** @test */
    public function it_does_create_a_fake_document_paginator()
    {
        $fake = DocumentPaginator::fake();

        $this->assertInstanceOf(DocumentPaginator::class, $fake);
    }
}
