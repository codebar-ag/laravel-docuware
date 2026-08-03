<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Data\SectionData;
use CodebarAg\DocuWare\Data\Users\GroupData;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\GetApplicationProperties;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Clip;
use CodebarAg\DocuWare\Requests\Documents\Sections\GetAllSectionsFromADocument;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetModifyGroups\GetAllGroupsForASpecificUser;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    config()->set('laravel-docuware.default', 'default');
    config()->set('laravel-docuware.instances.default', [
        'grant' => 'credentials', 'url' => 'https://example.docuware.cloud',
        'username' => 'alice', 'password' => 'secret',
    ]);
    config()->set('laravel-docuware.configurations.cache.driver', 'array');
    config()->set('laravel-docuware.configurations.cache.lifetime_in_seconds', 60);
    config()->set('laravel-docuware.configurations.request.timeout_in_seconds', 30);
    config()->set('laravel-docuware.configurations.client_id', 'x');
    config()->set('laravel-docuware.configurations.scope', 'y');
});

it('maps document sections from the Section key', function () {
    $client = clientWithMock([
        GetAllSectionsFromADocument::class => MockResponse::make(['Section' => [
            [
                'Id' => 's1', 'ContentType' => 'application/pdf', 'HaveMorePages' => false,
                'PageCount' => 1, 'FileSize' => 100, 'OriginalFileName' => 'a.pdf', 'AnnotationsPreview' => false,
            ],
        ]]),
    ]);

    $sections = $client->documents('cab-1')->sections('7');

    expect($sections)->toBeInstanceOf(Collection::class)
        ->and($sections)->toHaveCount(1)
        ->and($sections->first())->toBeInstanceOf(SectionData::class)
        ->and($sections->first()->id)->toBe('s1');
});

it('maps a clip result to DocumentData', function () {
    $client = clientWithMock([
        Clip::class => MockResponse::make(fakeDocumentPayload(99)),
    ]);

    $document = $client->documents('cab-1')->clip([['Id' => 1], ['Id' => 2]]);

    expect($document)->toBeInstanceOf(DocumentData::class)->and($document->id)->toBe(99);
});

it('maps a user groups list from the Item key', function () {
    $client = clientWithMock([
        GetAllGroupsForASpecificUser::class => MockResponse::make(['Item' => [
            ['Id' => 'g1', 'Name' => 'Admins', 'Active' => true],
        ]]),
    ]);

    $groups = $client->users()->groupsOf('user-1');

    expect($groups)->toBeInstanceOf(Collection::class)
        ->and($groups)->toHaveCount(1)
        ->and($groups->first())->toBeInstanceOf(GroupData::class);
});

it('returns raw application properties', function () {
    $client = clientWithMock([
        GetApplicationProperties::class => MockResponse::make(['Property' => [['Key' => 'k', 'Value' => 'v']]]),
    ]);

    $properties = $client->documents('cab-1')->applicationProperties('7');

    expect($properties)->toBeArray();
});
