<?php

namespace CodebarAg\DocuWare\Responses\Organization;

use CodebarAg\DocuWare\DTO\OrganizationIndex;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Saloon\Contracts\Response;

final class GetOrganizationsResponse
{
    public static function fromResponse(Response $response): Collection|Enumerable
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $organizations = $response->throw()->json('Organization');

        return collect($organizations)->map(fn (array $cabinet) => OrganizationIndex::fromJson($cabinet));
    }
}
