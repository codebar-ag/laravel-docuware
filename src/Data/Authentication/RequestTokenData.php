<?php

namespace CodebarAg\DocuWare\Data\Authentication;

use CodebarAg\DocuWare\Data\DocuWareData;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

final class RequestTokenData extends DocuWareData
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public string $scope,
        public int $expiresIn,
        public Carbon $expiresAt,
        public ?string $refreshToken = null,
        public ?string $idToken = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
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
}
