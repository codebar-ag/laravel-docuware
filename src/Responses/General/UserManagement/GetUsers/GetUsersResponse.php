<?php

namespace CodebarAg\DocuWare\Responses\General\UserManagement\GetUsers;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetUsersResponse
{
    use HandlesDocuWareResponse;

    /**
     * @return Collection<int, User>
     */
    public static function fromResponse(Response $response): Collection
    {
        $response = self::validated($response);

        $users = $response->throw()->json('User');

        return collect(JsonArrays::listOfRecords($users))->map(fn (array $user) => User::fromJson($user));
    }
}
