<?php

namespace CodebarAg\DocuWare\DTO\Authentication\OAuth;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

final class RequestToken
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function make(array $data): self
    {
        return new self(
            accessToken: Arr::get($data, 'access_token'),
            tokenType: Arr::get($data, 'token_type'),
            scope: Arr::get($data, 'scope'),
            expiresIn: Arr::get($data, 'expires_in'),
            expiresAt: Carbon::now()->addSeconds(Arr::get($data, 'expires_in')),
            refreshToken: Arr::get($data, 'refresh_token'),
            idToken: Arr::get($data, 'id_token'),
        );
    }

    public function __construct(
        public readonly string $accessToken,
        public readonly string $tokenType,
        public readonly string $scope,
        public readonly int $expiresIn,
        public readonly Carbon $expiresAt,
        public readonly ?string $refreshToken = null,
        public readonly ?string $idToken = null,
    ) {}
}
