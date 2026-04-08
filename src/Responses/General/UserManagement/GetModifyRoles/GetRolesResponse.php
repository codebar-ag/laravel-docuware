<?php

namespace CodebarAg\DocuWare\Responses\General\UserManagement\GetModifyRoles;

use CodebarAg\DocuWare\DTO\General\UserManagement\GetModifyRoles\Role;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetRolesResponse
{
    /**
     * @return Collection<int, Role>
     */
    public static function fromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $roles = $response->throw()->json('Item');

        return collect(JsonArrays::listOfRecords($roles))->map(fn (array $role) => Role::fromJson($role));
    }
}
