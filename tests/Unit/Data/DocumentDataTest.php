<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Data\Documents\DocumentFieldData;
use CodebarAg\DocuWare\Data\Documents\DocumentFlagsData;
use CodebarAg\DocuWare\Data\Documents\DocumentVersionData;

/**
 * Decode the document payload from a recorded Saloon fixture.
 *
 * @return array<string, mixed>
 */
function documentFixturePayload(): array
{
    $fixture = json_decode(
        (string) file_get_contents(__DIR__.'/../../Fixtures/saloon/file-cabinets/upload/create-data-record-with-file.json'),
        true,
    );

    return json_decode((string) $fixture['data'], true);
}

it('parses a real document fixture into DocumentData', function () {
    $payload = documentFixturePayload();
    $new = DocumentData::fromDocuWare($payload);

    expect($new->id)->toBe($payload['Id'])
        ->and($new->file_size)->toBe($payload['FileSize'])
        ->and($new->total_pages)->toBe($payload['TotalPages'])
        ->and($new->title)->toBe($payload['Title'])
        ->and($new->content_type)->toBe($payload['ContentType'])
        ->and($new->file_cabinet_id)->toBe($payload['FileCabinetId'])
        ->and($new->intellixTrust)->toBe($payload['IntellixTrust'] ?? null)
        ->and($new->organization_guid)->toBe($payload['OrganizationGuid'] ?? null)
        ->and($new->section_count)->toBe($payload['SectionCount'] ?? null)
        ->and($new->version_status)->toBe($payload['VersionStatus'] ?? null)
        ->and($new->has_text_annotation)->toBe($payload['HasTextAnnotation'] ?? null)
        ->and($new->have_more_total_pages)->toBe($payload['HaveMoreTotalPages'] ?? null)
        ->and($new->created_at)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($new->updated_at)->toBeInstanceOf(Carbon\Carbon::class);
});

it('maps nested document objects to Data classes', function () {
    $new = DocumentData::fromDocuWare(documentFixturePayload());

    expect($new->flags)->toBeInstanceOf(DocumentFlagsData::class)
        ->and($new->version)->toBeInstanceOf(DocumentVersionData::class)
        ->and($new->fields)->not->toBeNull()
        ->and($new->fields->every(fn ($f) => $f instanceof DocumentFieldData))->toBeTrue()
        ->and($new->links->first()?->rel)->toBeString();
});

it('keys fields by their FieldName', function () {
    $payload = documentFixturePayload();
    $new = DocumentData::fromDocuWare($payload);

    $expectedNames = collect($payload['Fields'])
        ->pluck('FieldName')
        ->filter(fn ($n) => is_string($n) && $n !== '')
        ->values()->all();

    expect($new->fields->keys()->all())->toBe($expectedNames)
        ->and($new->fields->get('DWEXTENSION'))->toBeInstanceOf(DocumentFieldData::class);
});

it('round-trips to array and supports an immutable wither', function () {
    $doc = DocumentData::fromDocuWare(documentFixturePayload());

    expect($doc->toArray())->toBeArray()
        ->and($doc->toArray()['title'])->toBe($doc->title);

    $renamed = $doc->copyWith(title: 'Changed Title');

    expect($renamed)->toBeInstanceOf(DocumentData::class)
        ->and($renamed->title)->toBe('Changed Title')
        ->and($renamed->id)->toBe($doc->id)
        ->and($doc->title)->not->toBe('Changed Title'); // original untouched
});
