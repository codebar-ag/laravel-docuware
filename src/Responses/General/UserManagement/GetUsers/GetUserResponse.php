<?php

namespace CodebarAg\DocuWare\Responses\General\UserManagement\GetUsers;

use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class GetUserResponse
{
    public static function fromResponse(Response $response): User
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $user = $response->throw()->json();

        return User::fromJson($user);
    }
}
