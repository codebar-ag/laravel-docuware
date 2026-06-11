<?php

use CodebarAg\DocuWare\Data\Organization\OrganizationData;

/**
 * @return array<string, mixed>
 */
function firstOrganizationPayload(): array
{
    $fixture = json_decode(
        (string) file_get_contents(__DIR__.'/../../Fixtures/saloon/get-organization.json'),
        true,
    );

    $data = json_decode((string) $fixture['data'], true);

    return $data['Organization'][0];
}

it('parses a real organization fixture into OrganizationData', function () {
    $payload = firstOrganizationPayload();
    $new = OrganizationData::fromDocuWare($payload);

    expect($new->id)->toBe((string) $payload['Id'])
        ->and($new->name)->toBe($payload['Name'])
        ->and($new->guid)->toBe($payload['Guid'] ?? null)
        ->and($new->isTwoStepVerificationEnabled)->toBe($payload['IsTwoStepVerificationEnabled'] ?? null)
        ->and($new->isTwoStepVerificationRequired)->toBe($payload['IsTwoStepVerificationRequired'] ?? null)
        ->and($new->links->count())->toBe(count($payload['Links'] ?? []));
});
