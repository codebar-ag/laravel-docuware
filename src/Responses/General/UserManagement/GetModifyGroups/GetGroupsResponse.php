<?php

namespace CodebarAg\DocuWare\Responses\General\UserManagement\GetModifyGroups;

use CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyGroups\Group;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Saloon\Http\Response;

final class GetGroupsResponse
{
    public static function fromResponse(Response $response): Enumerable|Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $groups = $response->throw()->json('Item');

        return collect($groups)->map(fn (array $group) => Group::fromJson($group));
    }
}
