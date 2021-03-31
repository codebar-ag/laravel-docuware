<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use Cache;
use Carbon\Carbon;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DocuWareTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $cookiePath = storage_path('app/.dwplatformauth');

        if (File::exists($cookiePath)) {
            $cookie = Str::of(File::get($cookiePath))
                ->trim()
                ->trim(PHP_EOL)
                ->trim();

            Cache::put(
                'docuware.cookies',
                [DocuWare::COOKIE_NAME => (string) $cookie],
                now()->addDay(),
            );

            return;
        }

        $cookie = (new DocuWare())->login();

        File::put($cookiePath, $cookie);
    }

    protected function tearDown(): void
    {
        if (File::exists(app_path('app/.dwplatformauth'))) {
            File::delete(app_path('app/.dwplatformauth'));

            (new DocuWare())->logout();
        }

        parent::tearDown();
    }

    /** @test */
    public function it_can_list_file_cabinets()
    {
        $fileCabinets = (new DocuWare())->getFileCabinets();

        $this->assertInstanceOf(Collection::class, $fileCabinets);
        $this->assertNotCount(0, $fileCabinets);
    }

    /** @test */
    public function it_can_list_fields_for_a_file_cabinet()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';

        $fields = (new DocuWare())->getFields($fileCabinetId);

        $this->assertInstanceOf(Collection::class, $fields);
        $this->assertNotCount(0, $fields);
    }

    /** @test */
    public function it_can_list_dialogs_for_a_file_cabinet()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';

        $dialogs = (new DocuWare())->getDialogs($fileCabinetId);

        $this->assertInstanceOf(Collection::class, $dialogs);
        $this->assertNotCount(0, $dialogs);
    }

    /** @test */
    public function it_can_list_values_for_a_select_list()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';
        $dialogId = '6a84f3da-7514-4116-86df-42b56acd19a7';
        $fieldName = 'DOKUMENTENTYP';

        $types = (new DocuWare())->getSelectList(
            $fileCabinetId,
            $dialogId,
            $fieldName,
        );

        $this->assertSame(['Auftrag', 'Offerte', 'Rechnung'], $types);
    }

    /** @test */
    public function it_can_show_a_document()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';
        $documentId = 1;

        $document = (new DocuWare())->getDocument(
            $fileCabinetId,
            $documentId,
        );

        $this->assertInstanceOf(Document::class, $document);
        $this->assertSame($documentId, $document->id);
        $this->assertSame($fileCabinetId, $document->file_cabinet_id);
    }

    /** @test */
    public function it_can_preview_a_document_image()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';
        $documentId = 1;

        $image = (new DocuWare())->getDocumentPreview(
            $fileCabinetId,
            $documentId,
        );

        $this->assertSame(11509, strlen($image));
    }

    /** @test */
    public function it_can_download_a_document()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';
        $documentId = 1;

        $contents = (new DocuWare())->downloadDocument(
            $fileCabinetId,
            $documentId,
        );

        $this->assertSame(37604, strlen($contents));
    }

    /** @test */
    public function it_can_download_multiple_documents()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';
        $documentIds = [1, 2];

        $contents = (new DocuWare())->downloadDocuments(
            $fileCabinetId,
            $documentIds,
        );

        $this->assertSame(67332, strlen($contents));
    }

    /** @test */
    public function it_can_update_a_document_value()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';
        $documentId = 6;
        $fieldName = 'DOCUMENT_TEXT';
        $newValue = 'Der neue Inhalt!';

        $response = (new DocuWare())->updateDocumentValue(
            $fileCabinetId,
            $documentId,
            $fieldName,
            $newValue,
        );

        $this->assertSame('Der neue Inhalt!', $response);
    }

    /** @test */
    public function it_can_upload_and_delete_a_document()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';
        $fileContent = '::fake-file-content::';
        $fileName = 'example.txt';

        $document = (new DocuWare())->uploadDocument(
            $fileCabinetId,
            $fileContent,
            $fileName,
        );
        (new DocuWare())->deleteDocument($fileCabinetId, $document->id);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertSame('example', $document->title);
    }

    /** @test */
    public function it_can_search_documents()
    {
        $fileCabinetId = 'f95f2093-e790-495b-af04-7d198a296c5e';
        $dialogId = '6a84f3da-7514-4116-86df-42b56acd19a7';
        $additionalFileCabinets = ['986ee421-9d6b-4a4b-837d-b3e61ea2e681'];

        $paginator = (new DocuWare())
            ->search()
            ->fileCabinet($fileCabinetId)
            ->dialog($dialogId)
            ->additionalFileCabinets($additionalFileCabinets)
            ->page(1)
            ->perPage(5)
            ->fulltext('test')
            ->dateFrom(Carbon::create(2021, 3))
            ->dateUntil(Carbon::create(2021, 3, 7))
            ->orderBy('DWSTOREDATETIME', 'desc')
            ->filter('DOKUMENTENTYP', 'Auftrag')
            ->get();

        $this->assertInstanceOf(DocumentPaginator::class, $paginator);
    }
}
