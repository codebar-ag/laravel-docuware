<?php

/**
 * Record or refresh tests/Fixtures/saloon/get-organization.json from a real DocuWare tenant.
 *
 * 1. Set DOCUWARE_URL, DOCUWARE_USERNAME, DOCUWARE_PASSWORD in the environment.
 * 2. Set DOCUWARE_RECORD_FIXTURES=true (e.g. in phpunit.xml or .env).
 * 3. Run: vendor/bin/pest tests/Manual/RecordGetOrganizationFixtureTest.php
 * 4. Review the JSON for secrets, then unset DOCUWARE_RECORD_FIXTURES.
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

    // Redact secret headers before writing, so committed fixtures never contain
    // session cookies or bearer tokens.
    $recorded = RecordedResponse::fromResponse($response);
    $secret = ['set-cookie', 'cookie', 'authorization'];
    $recorded->headers = collect($recorded->headers)
        ->mapWithKeys(fn ($value, $key) => [
            $key => in_array(strtolower((string) $key), $secret, true) ? 'REDACTED' : $value,
        ])
        ->all();

    file_put_contents($fixturePath, $recorded->toFile());

    expect(file_exists($fixturePath))->toBeTrue();
})->group('manual')->skip(
    fn () => ! filter_var(env('DOCUWARE_RECORD_FIXTURES', false), FILTER_VALIDATE_BOOLEAN),
    'Set DOCUWARE_RECORD_FIXTURES=true to record get-organization.json against your tenant.',
);
