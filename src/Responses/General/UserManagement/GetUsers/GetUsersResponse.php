<?php

namespace CodebarAg\DocuWare\Responses\General\UserManagement\GetUsers;

use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetUsersResponse
{
    /**
     * @return Collection<int, User>
     */
    public static function fromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $users = $response->throw()->json('User');

        return collect(JsonArrays::listOfRecords($users))->map(fn (array $user) => User::fromJson($user));
    }
}
