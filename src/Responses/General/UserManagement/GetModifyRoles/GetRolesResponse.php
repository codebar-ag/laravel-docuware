<?php

namespace CodebarAg\DocuWare\Responses\General\UserManagement\GetModifyRoles;

use CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyGroups\Group;
use CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyRoles\Role;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Saloon\Http\Response;

final class GetRolesResponse
{
    public static function fromResponse(Response $response): Enumerable|Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $roles = $response->throw()->json('Item');

        return collect($roles)->map(fn (array $role) => Role::fromJson($role));
    }
}
