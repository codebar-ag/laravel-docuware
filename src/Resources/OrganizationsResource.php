<?php

namespace CodebarAg\DocuWare\Resources;

use CodebarAg\DocuWare\Data\Organization\OrganizationData;
use CodebarAg\DocuWare\Requests\General\Organization\GetLoginToken;
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;
use Illuminate\Support\Collection;

/**
 * Organizations the authenticated user can access. Reached via `DocuWare::organizations()`.
 */
final class OrganizationsResource extends Resource
{
    /**
     * @return Collection<int, OrganizationData>
     */
    public function all(): Collection
    {
        $response = $this->send(new GetOrganization);

        $raw = $response->json('Organization', []);
        $organizations = is_array($raw) ? $raw : [];

        return collect($organizations)
            ->filter(fn ($organization) => is_array($organization))
            ->map(fn (array $organization) => OrganizationData::fromDocuWare($organization))
            ->values();
    }

    /**
     * Request a login token for single sign-on into DocuWare products.
     *
     * @param  array<int, string>  $targetProducts
     */
    public function loginToken(
        array $targetProducts = ['PlatformService'],
        string $usage = 'Multi',
        string $lifetime = '1.00:00:00',
    ): string {
        return (string) $this->send(new GetLoginToken($targetProducts, $usage, $lifetime))->body();
    }
}
