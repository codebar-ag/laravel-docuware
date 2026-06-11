<?php

namespace CodebarAg\DocuWare\Data\Authentication;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\Support\Field;
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
        // access_token and expires_in are auth-critical: a missing/invalid value must fail loudly
        // rather than silently mint an unusable, already-expired token.
        $expiresIn = Field::int($data, 'expires_in', self::class);

        return new self(
            accessToken: Field::string($data, 'access_token', self::class),
            tokenType: (string) Arr::get($data, 'token_type', ''),
            scope: (string) Arr::get($data, 'scope', ''),
            expiresIn: $expiresIn,
            expiresAt: Carbon::now()->addSeconds($expiresIn),
            refreshToken: Arr::get($data, 'refresh_token'),
            idToken: Arr::get($data, 'id_token'),
        );
    }
}
