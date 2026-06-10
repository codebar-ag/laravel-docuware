<?php

namespace CodebarAg\DocuWare\Responses\General\Organization;

use CodebarAg\DocuWare\DTO\General\Organization\Organization;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetOrganizationResponse
{
    /**
     * @return Collection<int, Organization>
     */
    public static function fromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $organizations = $response->throw()->json('Organization');

        return collect(JsonArrays::listOfRecords($organizations))->map(fn (array $organization) => Organization::fromJson($organization));
    }
}
