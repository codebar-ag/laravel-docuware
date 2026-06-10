<?php

namespace CodebarAg\DocuWare\Responses\General\UserManagement\GetModifyGroups;

use CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyGroups\Group;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetGroupsResponse
{
    /**
     * @return Collection<int, Group>
     */
    public static function fromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $groups = $response->throw()->json('Item');

        return collect(JsonArrays::listOfRecords($groups))->map(fn (array $group) => Group::fromJson($group));
    }
}
