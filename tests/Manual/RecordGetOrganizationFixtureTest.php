<?php

/**
 * Record or refresh tests/Fixtures/saloon/get-organization.json from a real DocuWare tenant.
 *
 * 1. Set DOCUWARE_URL, DOCUWARE_USERNAME, DOCUWARE_PASSWORD in the environment.
 * 2. Remove ->skip() below temporarily.
 * 3. Run: vendor/bin/pest tests/Manual/RecordGetOrganizationFixtureTest.php
 * 4. Review the JSON for secrets, then restore ->skip().
 */

use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;
use Saloon\Data\RecordedResponse;
use Saloon\MockConfig;

it('records get-organization Saloon fixture', function () {
    MockConfig::setFixturePath(dirname(__DIR__).'/Fixtures/saloon');

    $response = getConnector()->send(new GetOrganization);

    expect($response->successful())
        ->toBeTrue('Expected successful GetOrganization response before writing fixture; HTTP '.$response->status().'.');

    $contentType = (string) $response->header('Content-Type');
    expect($contentType === '' || str_contains(strtolower($contentType), 'json'))
        ->toBeTrue('Expected JSON Content-Type from GetOrganization; got: '.($contentType !== '' ? $contentType : '(empty)'));

    $fixturePath = dirname(__DIR__).'/Fixtures/saloon/get-organization.json';

    file_put_contents(
        $fixturePath,
        RecordedResponse::fromResponse($response)->toFile()
    );

    expect(file_exists($fixturePath))->toBeTrue();
})->group('manual')->skip('Remove skip to record fixtures against a real DocuWare system.');
