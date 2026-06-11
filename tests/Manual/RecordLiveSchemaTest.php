<?php

/**
 * Dump real DocuWare request/response JSON from the live tenant configured in phpunit.xml
 * into tests/Fixtures/schema/<endpoint>.json. These are working artifacts used to shape the
 * DTOs and seed Saloon fixtures — they are NOT assertions.
 *
 * Run: vendor/bin/pest tests/Manual/RecordLiveSchemaTest.php
 * (guarded by DOCUWARE_CAPTURE_SCHEMA=true so it never runs in normal suites/CI).
 */

use CodebarAg\DocuWare\Requests\Authentication\OAuth\GetResponsibleIdentityService;
use CodebarAg\DocuWare\Requests\Documents\Sections\GetAllSectionsFromADocument;
use CodebarAg\DocuWare\Requests\Documents\Stamps\GetDocumentAnnotations;
use CodebarAg\DocuWare\Requests\Documents\Stamps\GetStamps;
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetAllDialogs;
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetASpecificDialog;
use CodebarAg\DocuWare\Requests\FileCabinets\General\GetFileCabinetInformation;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetDocumentsFromAFileCabinet;
use CodebarAg\DocuWare\Requests\General\Organization\GetAllFileCabinetsAndDocumentTrays;
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\SoloRequest;

beforeEach(function () {
    if (! filter_var(env('DOCUWARE_CAPTURE_SCHEMA', false), FILTER_VALIDATE_BOOLEAN)) {
        $this->markTestSkipped('Set DOCUWARE_CAPTURE_SCHEMA=true to capture live schema.');
    }
});

function schemaDir(): string
{
    $dir = dirname(__DIR__).'/Fixtures/schema';
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    return $dir;
}

/**
 * Send a request and persist whatever comes back: pretty JSON when parseable, raw body otherwise.
 * Never throws — records HTTP status + body so we can see auth/HTML errors too.
 */
function dumpSchema(string $name, Connector $connector, Request|SoloRequest $request): void
{
    try {
        $response = $request instanceof SoloRequest ? $request->send() : $connector->send($request);
        $body = (string) $response->body();
        $decoded = json_decode($body, true);
        $payload = is_array($decoded)
            ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            : "// HTTP {$response->status()} — non-JSON body\n".$body;
    } catch (Throwable $e) {
        $payload = '// ERROR: '.$e::class.': '.$e->getMessage();
    }

    file_put_contents(schemaDir().'/'.$name.'.json', $payload);
    fwrite(STDERR, "captured: {$name}\n");
}

it('captures live schema for read endpoints', function () {
    $connector = getConnector();
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');
    $url = config('laravel-docuware.credentials.url');

    // --- OAuth discovery (confirms RefreshTokenSupported / windows_auth_endpoint) ---
    dumpSchema('home-identity-service-info', $connector, new GetResponsibleIdentityService($url));

    // --- Organization / cabinets ---
    dumpSchema('organizations', $connector, new GetOrganization);
    dumpSchema('file-cabinets', $connector, new GetAllFileCabinetsAndDocumentTrays);

    // --- File cabinet information (Fields structure) ---
    dumpSchema('file-cabinet-information', $connector, new GetFileCabinetInformation($fileCabinetId));

    // --- Dialogs (Fields + Query structure) ---
    dumpSchema('dialogs-all', $connector, new GetAllDialogs($fileCabinetId));
    dumpSchema('dialog-specific', $connector, new GetASpecificDialog($fileCabinetId, $dialogId));

    // --- Stamps ---
    dumpSchema('stamps', $connector, new GetStamps($fileCabinetId));

    // --- Documents list ---
    dumpSchema('documents-list', $connector, new GetDocumentsFromAFileCabinet($fileCabinetId, perPage: 5));

    // --- Drill into the first document if any exist ---
    try {
        $docs = $connector->send(new GetDocumentsFromAFileCabinet($fileCabinetId, perPage: 5));
        $items = $docs->json('Items') ?? [];
        if (! empty($items)) {
            $documentId = $items[0]['Id'];
            fwrite(STDERR, "using documentId={$documentId}\n");
            dumpSchema('document-specific', $connector, new GetASpecificDocumentFromAFileCabinet($fileCabinetId, (string) $documentId));
            dumpSchema('document-sections', $connector, new GetAllSectionsFromADocument($fileCabinetId, (string) $documentId));
            dumpSchema('document-annotations', $connector, new GetDocumentAnnotations($fileCabinetId, $documentId));
        } else {
            fwrite(STDERR, "no documents in cabinet — skipping document drill-down\n");
        }
    } catch (Throwable $e) {
        fwrite(STDERR, 'document drill-down error: '.$e->getMessage()."\n");
    }

    expect(is_dir(schemaDir()))->toBeTrue();
})->group('manual');
