<?php

namespace CodebarAg\DocuWare\Responses\General\Organization;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\General\Organization\Organization;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetOrganizationResponse
{
    use HandlesDocuWareResponse;

    /**
     * @return Collection<int, Organization>
     */
    public static function fromResponse(Response $response): Collection
    {
        $response = self::validated($response);

        $organizations = $response->throw()->json('Organization');

        return collect(JsonArrays::listOfRecords($organizations))->map(fn (array $organization) => Organization::fromJson($organization));
    }
}
