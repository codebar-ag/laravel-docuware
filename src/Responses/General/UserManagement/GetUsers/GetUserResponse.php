<?php

namespace CodebarAg\DocuWare\Responses\General\UserManagement\GetUsers;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use Saloon\Http\Response;

final class GetUserResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): User
    {
        $response = self::validated($response);

        $user = $response->throw()->json();

        return User::fromJson($user);
    }
}
