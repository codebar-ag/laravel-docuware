<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use Carbon\Carbon;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\DocumentField;
use CodebarAg\DocuWare\DTO\DocumentIndex;
use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\Auth;
use CodebarAg\DocuWare\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use CodebarAg\DocuWare\Facades\DocuWare;

class DocuWareTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $cookiePath = storage_path('app/.dwplatformauth');

        if (File::exists($cookiePath)) {
            $cookie = Str::of(File::get($cookiePath))
                ->trim()
                ->trim(PHP_EOL)
                ->trim();

            Cache::put(
                Auth::CACHE_KEY,
                [Auth::COOKIE_NAME => (string) $cookie],
                now()->addDay(),
            );

            return;
        }

        (new DocuWare())->login();

        File::put($cookiePath, Auth::cookies()[Auth::COOKIE_NAME]);
    }

    protected function tearDown(): void
    {
        if (File::exists(app_path('app/.dwplatformauth'))) {
            File::delete(app_path('app/.dwplatformauth'));

            (new DocuWare())->logout();
        }

        parent::tearDown();
    }

    /** @test @group list */
    public function it_can_list_file_cabinets()
    {
        $fileCabinets = (new DocuWare())->getFileCabinets();

        $this->assertInstanceOf(Collection::class, $fileCabinets);
        $this->assertNotCount(0, $fileCabinets);
        Event::assertDispatched(DocuWareResponseLog::class);
    }

    /** @test */
    public function it_can_list_fields_for_a_file_cabinet()
    {
        $fileCabinetId = '75c7cee7-ff04-475f-bc43-44b8ec3f7754';

        $fields = (new DocuWare())->getFields($fileCabinetId);

        $this->assertInstanceOf(Collection::class, $fields);
        $this->assertNotCount(0, $fields);
        Event::assertDispatched(DocuWareResponseLog::class);
    }

    /** @test
     */
    public function it_can_list_dialogs_for_a_file_cabinet()
    {
        $fileCabinetId = '75c7cee7-ff04-475f-bc43-44b8ec3f7754';

        $dialogs = (new DocuWare())->getDialogs($fileCabinetId);

        $this->assertInstanceOf(Collection::class, $dialogs);
        $this->assertNotCount(0, $dialogs);
        Event::assertDispatched(DocuWareResponseLog::class);
    }

    /** @test */
    public function it_can_list_values_for_a_select_list()
    {
        $fileCabinetId = '75c7cee7-ff04-475f-bc43-44b8ec3f7754';
        $dialogId = 'adfa26ea-3148-4365-b71f-45a01f0e95a0';
        $fieldName = 'DOCUMENT_TYPE';

        $types = (new DocuWare())->getSelectList(
            $fileCabinetId,
            $dialogId,
            $fieldName,
        );

        $this->assertNotCount(0, $types);
        Event::assertDispatched(DocuWareResponseLog::class);
    }

    /** @test */
    public function it_can_show_a_document()
    {
        $fileCabinetId = '75c7cee7-ff04-475f-bc43-44b8ec3f7754';
        $documentId = 8;

        $document = (new DocuWare())->getDocument(
            $fileCabinetId,
            $documentId,
        );

        $this->assertInstanceOf(Document::class, $document);
        $this->assertSame($documentId, $document->id);
        $this->assertSame($fileCabinetId, $document->file_cabinet_id);
        Event::assertDispatched(DocuWareResponseLog::class);
    }

    /** @test */
    public function it_can_preview_a_document_image()
    {
        $fileCabinetId = '75c7cee7-ff04-475f-bc43-44b8ec3f7754';
        $documentId = 8;

        $image = (new DocuWare())->getDocumentPreview(
            $fileCabinetId,
            $documentId,
        );

        $this->assertSame(2126, strlen($image));
        Event::assertDispatched(DocuWareResponseLog::class);
    }

    /** @test */
    public function it_can_download_a_document()
    {
        $fileCabinetId = '75c7cee7-ff04-475f-bc43-44b8ec3f7754';
        $documentId = 8;

        $contents = (new DocuWare())->downloadDocument(
            $fileCabinetId,
            $documentId,
        );

        $this->assertSame(4911, strlen($contents));
        Event::assertDispatched(DocuWareResponseLog::class);
    }

    /** @test */
    public function it_can_download_multiple_documents()
    {
        $fileCabinetId = '75c7cee7-ff04-475f-bc43-44b8ec3f7754';
        $documentIds = [7, 8];

        $contents = (new DocuWare())->downloadDocuments(
            $fileCabinetId,
            $documentIds,
        );

        $this->assertSame(3818, strlen($contents));
        Event::assertDispatched(DocuWareResponseLog::class);
    }

    /** @test */
    public function it_can_update_a_document_value()
    {
        $fileCabinetId = '75c7cee7-ff04-475f-bc43-44b8ec3f7754';
        $documentId = 7;
        $fieldName = 'DOCUMENT_LABEL';
        $newValue = 'Der neue Inhalt!';

        $response = (new DocuWare())->updateDocumentValue(
            $fileCabinetId,
            $documentId,
            $fieldName,
            $newValue,
        );

        $this->assertSame('Der neue Inhalt!', $response);
        Event::assertDispatched(DocuWareResponseLog::class);
    }

    /** @test */
    public function it_can_upload_document_with_index_values_and_delete_it()
    {
        $this->markTestSkipped();
        $fileCabinetId = '75c7cee7-ff04-475f-bc43-44b8ec3f7754';
        $fileContent = '::fake-file-content::';
        $fileName = 'example.txt';

        $document = (new DocuWare())->uploadDocument(
            $fileCabinetId,
            $fileContent,
            $fileName,
            collect([
                DocumentIndex::make('DOCUMENT_LABEL', '::text::'),
            ]),
        );
        (new DocuWare())->deleteDocument($fileCabinetId, $document->id);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertSame('example', $document->title);
        tap($document->fields['DOCUMENT_LABEL'], function (DocumentField $field) {
            $this->assertSame($field->name, 'DOCUMENT_LABEL');
            $this->assertSame($field->type, 'String');
            $this->assertSame($field->value, '::text::');
        });
        Event::assertDispatched(DocuWareResponseLog::class);
    }

    /** @test */
    public function it_can_search_documents()
    {
        $fileCabinetId = '75c7cee7-ff04-475f-bc43-44b8ec3f7754';
        $dialogId = '8f57d5d6-b11c-4b53-b2c6-335ea0bc8238';

        $paginator = (new DocuWare())
            ->search()
            ->fileCabinet($fileCabinetId)
            ->dialog($dialogId)
            ->page(1)
            ->perPage(5)
            ->fulltext('test')
            ->dateFrom(Carbon::create(2021))
            ->dateUntil(now())
            ->filter('DOCUMENT_TYPE', 'Abrechnung')
            ->orderBy('DWSTOREDATETIME', 'desc')
            ->get();

        $this->assertInstanceOf(DocumentPaginator::class, $paginator);
        Event::assertDispatched(DocuWareResponseLog::class);
    }

    /** @test */
    public function it_can_search_documents_with_null_values()
    {
        $fileCabinetIds = [
            '75c7cee7-ff04-475f-bc43-44b8ec3f7754',
            '86a15ac1-ea58-4510-9905-6cb13c905a4f',
        ];

        $paginator = (new DocuWare())
            ->search()
            ->fileCabinets($fileCabinetIds)
            ->page(null)
            ->perPage(null)
            ->fulltext(null)
            ->dateFrom(null)
            ->dateUntil(null)
            ->filter('DOCUMENT_TYPE', null)
            ->orderBy('DWSTOREDATETIME', null)
            ->get();

        $this->assertInstanceOf(DocumentPaginator::class, $paginator);
        Event::assertDispatched(DocuWareResponseLog::class);
    }

    /** @test */
    public function it_can_create_encrypted_url_for_a_document_in_a_file_cabinet()
    {
        $fileCabinetId = '75c7cee7-ff04-475f-bc43-44b8ec3f7754';
        $documentId = 7;

        $url = (new DocuWare())
            ->url()
            ->fileCabinet($fileCabinetId)
            ->document($documentId)
            ->validUntil(now()->addMinute())
            ->make();

        $this->assertStringStartsWith(
            'https://vanta.docuware.cloud/DocuWare/Platform/WebClient/Integration?ep=',
            $url,
        );
    }

    /** @test */
    public function it_can_create_encrypted_url_for_a_document_in_a_basket()
    {
        $basketId = 'b_8ae317d3-20cd-4097-b828-4825ce0e0403';
        $documentId = 1;

        $url = (new DocuWare())
            ->url()
            ->basket($basketId)
            ->document($documentId)
            ->validUntil(now()->addMinute())
            ->make();

        $this->assertStringStartsWith(
            'https://vanta.docuware.cloud/DocuWare/Platform/WebClient/Integration?ep=',
            $url,
        );
    }
}
