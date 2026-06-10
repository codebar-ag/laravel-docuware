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
        );
    }

    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public string $scope,
        public int $expiresIn,
        public Carbon $expiresAt,
    ) {}
}
