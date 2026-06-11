<?php

use CodebarAg\DocuWare\Client\DocuWareClient;
use CodebarAg\DocuWare\Data\Authentication\RequestTokenData;
use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Data\Documents\FieldData;
use CodebarAg\DocuWare\Data\FileCabinets\DialogData;
use CodebarAg\DocuWare\Data\Write\IndexFields;
use CodebarAg\DocuWare\DocuWareManager;
use CodebarAg\DocuWare\Facades\DocuWare;
use CodebarAg\DocuWare\Tests\Support\DocuWareFixture;
use CodebarAg\DocuWare\Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use Saloon\Http\Response;

uses(TestCase::class)
    ->in(__DIR__);

uses()
    ->group('integration')
    ->beforeEach(function () {
        $this->cabinet = config('laravel-docuware.tests.file_cabinet_id');

        clearCabinet($this->cabinet);
    })
    ->afterEach(function () {
        deactivateTestUsers();
    })
    ->in('Integration');

/**
 * Delete every document in the test cabinet, then empty the trash bin.
 */
function clearCabinet(string $cabinet): void
{
    foreach (DocuWare::documents($cabinet)->search()->perPage(1000)->get()->documents as $document) {
        try {
            DocuWare::documents($cabinet)->delete($document->id);
        } catch (Throwable) {
            // Skip documents that cannot be deleted (e.g. locked in an in-progress workflow
            // under version management) so cleanup never aborts the whole suite.
        }
    }

    emptyTrash();
}

function emptyTrash(): void
{
    $page = DocuWare::trash()->search(perPage: 1000);

    $ids = $page->documents
        ->map(fn (Collection $row) => $row->get('ID') ?? $row->get('Id'))
        ->filter()
        ->values()
        ->all();

    if ($ids !== []) {
        DocuWare::trash()->delete($ids);
    }
}

/**
 * Deactivate any leftover test users (the only API-supported "removal").
 */
function deactivateTestUsers(): void
{
    DocuWare::users()->all()
        ->filter(fn ($user) => Str::contains($user->email, 'test@example.test') && $user->active === true)
        ->each(fn ($user) => DocuWare::users()->update($user->copyWith(active: false)));
}

/**
 * Discover the test cabinet's writable (User-scope) index fields, keyed by DocuWare field type.
 *
 * @return Collection<string, FieldData>
 */
function sandboxUserFields(string $cabinet): Collection
{
    return DocuWare::fileCabinets()->fields($cabinet)
        ->filter(fn (FieldData $field) => $field->isUser())
        ->keyBy(fn (FieldData $field) => $field->type);
}

/**
 * DB name of a writable field of the given DocuWare type (e.g. 'Text', 'Numeric', 'Date').
 * Skips the test if the cabinet has no such field, keeping the suite portable across cabinets.
 */
function sandboxFieldName(string $cabinet, string $type): string
{
    $field = sandboxUserFields($cabinet)->get($type);

    if (! $field instanceof FieldData) {
        test()->markTestSkipped("Sandbox cabinet has no writable field of type [{$type}].");
    }

    return $field->name;
}

/**
 * Upload a fresh test document into the test cabinet against its discovered Text field.
 */
function uploadTestDocument(string $cabinet, ?string $value = null, ?string $fileName = 'example.txt'): DocumentData
{
    $textField = sandboxFieldName($cabinet, 'Text');

    return DocuWare::documents($cabinet)->store(
        fileContent: $fileName !== null ? '::fake-file-content::' : null,
        fileName: $fileName,
        indexes: IndexFields::make()->text($textField, $value ?? 'value-'.Str::random(8)),
    );
}

/**
 * Discover the cabinet's default Search dialog id (falling back to any Search dialog).
 */
function sandboxSearchDialogId(string $cabinet): string
{
    $dialogs = DocuWare::dialogs($cabinet)->all()
        ->filter(fn (DialogData $dialog) => $dialog->type === 'Search');

    $dialog = $dialogs->firstWhere(fn (DialogData $d) => $d->isDefault === true) ?? $dialogs->first();

    if (! $dialog instanceof DialogData) {
        test()->markTestSkipped('Sandbox cabinet has no Search dialog.');
    }

    return $dialog->id;
}

/**
 * JSON body for the AddDocumentAnnotations integration test.
 * Set DOCUWARE_TESTS_STAMP_ID to use a StampPlacement payload, or DOCUWARE_TESTS_ANNOTATION_JSON
 * to provide a full JSON object as a string.
 *
 * @return array<string, mixed>
 */
function integrationTestAnnotationPayload(): array
{
    $raw = env('DOCUWARE_TESTS_ANNOTATION_JSON');
    if (is_string($raw) && $raw !== '') {
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($decoded)) {
            throw new InvalidArgumentException('DOCUWARE_TESTS_ANNOTATION_JSON must decode to an array.');
        }

        return $decoded;
    }

    $stampId = env('DOCUWARE_TESTS_STAMP_ID');

    if (is_string($stampId) && $stampId !== '') {
        $items = [
            ['$type' => 'StampPlacement', 'StampId' => $stampId, 'Layer' => 1],
        ];
    } else {
        $items = [
            [
                '$type' => 'Annotation',
                'Layer' => [
                    [
                        'Id' => 1,
                        'Items' => [
                            [
                                '$type' => 'TextEntry',
                                'Location' => ['Left' => 100, 'Top' => 100, 'Width' => 800, 'Height' => 400],
                                'Value' => 'laravel-docuware integration',
                                'Font' => [
                                    'FontName' => 'Arial', 'Bold' => false, 'Italic' => false,
                                    'Underlined' => false, 'StrikeThrough' => false, 'FontSize' => 200, 'Spacing' => 0,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    return [
        'Annotations' => [
            [
                'PageNumber' => 0,
                'SectionNumber' => 0,
                'AnnotationsPlacement' => ['Items' => $items],
            ],
        ],
    ];
}

/**
 * Send a request through the native connector via a Saloon fixture so the full real response is
 * captured (redacted) for replay. Replays the recorded fixture offline; records the real response
 * when missing or when DOCUWARE_RECORD_FIXTURES=true.
 */
function recordFixture(Request $request, string $name): Response
{
    $path = __DIR__.'/Fixtures/saloon/'.$name.'.json';

    if (filter_var(env('DOCUWARE_RECORD_FIXTURES', false), FILTER_VALIDATE_BOOLEAN) && file_exists($path)) {
        unlink($path);
    }

    $connector = DocuWare::instance()->connector();
    $connector->withMockClient(new MockClient([
        $request::class => new DocuWareFixture($name),
    ]));

    return $connector->send($request);
}

/**
 * Poll a document until it has been processed (sections present), then return it.
 */
function refreshDocumentAfterProcessing(string $cabinet, int $documentId): DocumentData
{
    for ($attempt = 1; $attempt <= 60; $attempt++) {
        $document = DocuWare::documents($cabinet)->find($documentId);

        if ($document->total_pages > 0 && $document->sections !== null && $document->sections->isNotEmpty()) {
            return $document;
        }

        Sleep::for(250)->milliseconds();
    }

    return DocuWare::documents($cabinet)->find($documentId);
}

/**
 * Resolve the default-instance client (used by tests that need the raw connector).
 */
function docuwareClient(): DocuWareClient
{
    return DocuWare::instance();
}

/**
 * Build the default-instance client with a pre-seeded OAuth token and a Saloon mock client
 * attached, for offline resource-layer tests.
 *
 * @param  array<int, MockResponse>|array<class-string, MockResponse>  $responses
 */
function clientWithMock(array $responses): DocuWareClient
{
    $manager = app(DocuWareManager::class);
    $config = $manager->resolveConfig('default');

    $token = new RequestTokenData(
        'seeded', 'Bearer', 'docuware.platform', 3600, Carbon::now()->addHour(),
    );
    Cache::store($config->cacheDriver)
        ->put('docuware.oauth.'.$config->identifier(), Crypt::encrypt($token), 3600);

    $client = $manager->instance();
    $client->connector()->withMockClient(new MockClient($responses));

    return $client;
}

/**
 * A minimal DocuWare document payload for synthetic search/find mocks.
 *
 * @return array<string, mixed>
 */
function fakeDocumentPayload(int $id): array
{
    return [
        'Id' => $id,
        'FileSize' => 1000 + $id,
        'TotalPages' => 1,
        'Title' => 'Doc '.$id,
        'ContentType' => 'application/pdf',
        'FileCabinetId' => 'cab-1',
        'CreatedAt' => '/Date(1700000000000)/',
        'LastModified' => '/Date(1700000500000)/',
    ];
}
