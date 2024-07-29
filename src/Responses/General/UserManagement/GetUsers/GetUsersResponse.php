<?php

namespace CodebarAg\DocuWare\Responses\General\UserManagement\GetUsers;

use CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers\User;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Saloon\Http\Response;

final class GetUsersResponse
{
    public static function fromResponse(Response $response): Enumerable|Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $users = $response->throw()->json('User');

        return collect($users)->map(fn (array $user) => User::fromJson($user));
    }
}
