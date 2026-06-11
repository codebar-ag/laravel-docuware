<?php

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;
use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\DTO\Documents\Field;
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\DeleteDocuments;
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\DeleteDocument;
use CodebarAg\DocuWare\Requests\Fields\GetFieldsRequest;
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetAllDialogs;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetDocumentsFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\UpdateUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsers;
use CodebarAg\DocuWare\Tests\Support\DocuWareFixture;
use CodebarAg\DocuWare\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Request;
use Saloon\Http\Response;

uses(TestCase::class)
    ->in(__DIR__);

uses()
    ->group('live')
    ->beforeEach(function () {
        $this->connector = getConnector();

        clearFiles($this->connector);
    })
    ->afterEach(function () {
        setUsersInactive($this->connector);
    })
    ->in('Integration');

function clearFiles(DocuWareConnector $connector): void
{
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $paginator = $connector->send(new GetDocumentsFromAFileCabinet($fileCabinetId))->dto();

    if ($paginator->documents->isEmpty()) {
        emptyTrashForConnector($connector);

        return;
    }

    foreach ($paginator->documents as $document) {
        try {
            $connector->send(new DeleteDocument(
                $fileCabinetId,
                $document->id,
            ))->dto();
        } catch (Throwable) {
            // Skip documents that cannot be deleted (e.g. locked in an in-progress
            // workflow under version management). Leaving them in place must not
            // abort the per-test cleanup for every other integration test.
        }
    }

    emptyTrashForConnector($connector);
}

function emptyTrashForConnector(DocuWareConnector $connector): void
{
    $paginatorRequest = (new DocuWare)
        ->searchRequestBuilder()
        ->trashBin()
        ->perPage(1000)
        ->get();

    $paginator = $connector->send($paginatorRequest)->dto();

    if ($paginator->total > 0) {
        $connector->send(new DeleteDocuments($paginator->mappedDocuments->pluck('ID')->all()))->dto();
    }
}

function setUsersInactive(DocuWareConnector $connector): void
{
    $response = $connector->send(new GetUsers);

    $users = $response->dto()->filter(function ($user) {
        return Str::contains($user->email, 'test@example.test') && $user->active === true;
    });

    foreach ($users as $user) {
        $user->active = false;

        $connector->send(new UpdateUser($user));
    }
}

/**
 * JSON body for AddDocumentAnnotations integration test.
 * Set DOCUWARE_TESTS_ANNOTATION_JSON to override (full JSON object as a string).
 * Or set DOCUWARE_TESTS_STAMP_ID to use a StampPlacement payload (optional Location).
 *
 * @return array<string, mixed>
 */
function integrationTestAnnotationPayload(): array
{
    $raw = env('DOCUWARE_TESTS_ANNOTATION_JSON');
    if (is_string($raw) && $raw !== '') {
        try {
            /** @var mixed $decoded */
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidArgumentException('DOCUWARE_TESTS_ANNOTATION_JSON must be valid JSON: '.$e->getMessage(), 0, $e);
        }
        if (! is_array($decoded)) {
            throw new InvalidArgumentException('DOCUWARE_TESTS_ANNOTATION_JSON must decode to an array.');
        }

        return $decoded;
    }

    $stampId = env('DOCUWARE_TESTS_STAMP_ID');
    if (is_string($stampId) && $stampId !== '') {
        return [
            'Annotations' => [
                [
                    'PageNumber' => 0,
                    'SectionNumber' => 0,
                    'AnnotationsPlacement' => [
                        'Items' => [
                            [
                                '$type' => 'StampPlacement',
                                'StampId' => $stampId,
                                'Layer' => 1,
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
                'AnnotationsPlacement' => [
                    'Items' => [
                        [
                            '$type' => 'Annotation',
                            'Layer' => [
                                [
                                    'Id' => 1,
                                    'Items' => [
                                        [
                                            '$type' => 'TextEntry',
                                            'Location' => [
                                                'Left' => 100,
                                                'Top' => 100,
                                                'Width' => 800,
                                                'Height' => 400,
                                            ],
                                            'Value' => 'laravel-docuware integration',
                                            'Font' => [
                                                'FontName' => 'Arial',
                                                'Bold' => false,
                                                'Italic' => false,
                                                'Underlined' => false,
                                                'StrikeThrough' => false,
                                                'FontSize' => 200,
                                                'Spacing' => 0,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];
}

/**
 * @throws Throwable
 */
function getConnector(): DocuWareConnector
{
    return new DocuWareConnector(new ConfigWithCredentials(
        username: config('laravel-docuware.credentials.username'),
        password: config('laravel-docuware.credentials.password'),
    ));
}

/**
 * Send a request through a Saloon fixture so the full real response is captured for replay.
 *
 * - Replays the recorded fixture if it exists (offline `composer test`).
 * - Records the real response (redacted) when the fixture is missing, or always when
 *   DOCUWARE_RECORD_FIXTURES=true (live run that refreshes the capture).
 *
 * Uses a throwaway connector so the mock client never leaks into the shared per-test
 * connector; the OAuth token is shared via the cache, so no extra auth round-trip occurs.
 *
 * @throws Throwable
 */
function recordFixture(Request $request, string $name): Response
{
    $path = __DIR__.'/Fixtures/saloon/'.$name.'.json';

    if (filter_var(env('DOCUWARE_RECORD_FIXTURES', false), FILTER_VALIDATE_BOOLEAN) && file_exists($path)) {
        unlink($path);
    }

    $connector = getConnector();
    $connector->withMockClient(new MockClient([
        $request::class => new DocuWareFixture($name),
    ]));

    return $connector->send($request);
}

/**
 * Discover the sandbox cabinet's writable (User-scope) index fields, keyed by DocuWare field type.
 *
 * Lets tests generate their own data against whatever fields the cabinet actually exposes
 * (this sandbox names one field per type: TEXT/NUMBER/COMMENT/DATE/KEYWORD/DECIMAL/DATETIME/TABLE)
 * instead of hardcoding field DB names that only exist in one specific cabinet.
 *
 * @return Collection<string, Field> DWFieldType => Field
 */
function sandboxUserFields(DocuWareConnector $connector): Collection
{
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    return collect($connector->send(new GetFieldsRequest($fileCabinetId))->dto())
        ->filter(fn (Field $field) => $field->isUser())
        ->keyBy(fn (Field $field) => $field->type);
}

/**
 * DB name of a writable field of the given DocuWare type (e.g. 'Text', 'Numeric', 'Date').
 * Skips the test if the cabinet has no such field, so the suite stays portable across cabinets.
 */
function sandboxFieldName(DocuWareConnector $connector, string $type): string
{
    $field = sandboxUserFields($connector)->get($type);

    if (! $field instanceof Field) {
        test()->markTestSkipped("Sandbox cabinet has no writable field of type [{$type}].");
    }

    return $field->name;
}

/**
 * Upload a fresh test document into the sandbox cabinet and return its DTO.
 * Generates its own index values against the discovered Text field — no hardcoded field names.
 *
 * @throws Throwable
 */
function uploadTestDocument(DocuWareConnector $connector, ?string $value = null, ?string $fileName = 'example.txt'): Document
{
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $textField = sandboxFieldName($connector, 'Text');

    return $connector->send(new CreateDataRecord(
        $fileCabinetId,
        $fileName !== null ? '::fake-file-content::' : null,
        $fileName,
        collect([IndexTextDTO::make($textField, $value ?? 'value-'.Str::random(8))]),
    ))->dto();
}

/**
 * Discover the cabinet's default Search dialog id (falling back to any Search dialog),
 * so tests never hardcode a dialog id.
 */
function sandboxSearchDialogId(DocuWareConnector $connector): string
{
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $dialogs = collect($connector->send(new GetAllDialogs($fileCabinetId))->dto())
        ->filter(fn ($dialog) => $dialog->type === 'Search');

    $dialog = $dialogs->firstWhere(fn ($d) => $d->isDefault === true) ?? $dialogs->first();

    if ($dialog === null) {
        test()->markTestSkipped('Sandbox cabinet has no Search dialog.');
    }

    return $dialog->id;
}

function cleanup($connector, $fileCabinetId): void
{
    $paginator = $connector->send(new GetDocumentsFromAFileCabinet(
        $fileCabinetId
    ))->dto();

    foreach ($paginator->documents as $document) {
        $connector->send(new DeleteDocument(
            $fileCabinetId,
            $document->id,
        ))->dto();
    }
}

function documentLooksProcessed(Document $document): bool
{
    return $document->total_pages > 0
        && $document->sections !== null
        && $document->sections->isNotEmpty();
}

function refreshDocumentAfterProcessing(DocuWareConnector $connector, string $fileCabinetId, int $documentId): Document
{
    $maxAttempts = 60;
    $sleepMs = 250;

    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        $document = $connector->send(new GetASpecificDocumentFromAFileCabinet(
            $fileCabinetId,
            $documentId
        ))->dto();

        if (documentLooksProcessed($document)) {
            return $document;
        }

        Sleep::for($sleepMs)->milliseconds();
    }

    return $connector->send(new GetASpecificDocumentFromAFileCabinet(
        $fileCabinetId,
        $documentId
    ))->dto();
}

function uploadFiles(DocuWareConnector $connector, $fileCabinetId, $path): array
{
    $document = $connector->send(new CreateDataRecord(
        $fileCabinetId,
        file_get_contents($path.'/test-1.pdf'),
        'test-1.pdf',
    ))->dto();

    $document2 = $connector->send(new CreateDataRecord(
        $fileCabinetId,
        file_get_contents($path.'/test-2.pdf'),
        'test-2.pdf',
    ))->dto();

    $document = refreshDocumentAfterProcessing($connector, $fileCabinetId, $document->id);
    $document2 = refreshDocumentAfterProcessing($connector, $fileCabinetId, $document2->id);

    return [$document, $document2];
}
