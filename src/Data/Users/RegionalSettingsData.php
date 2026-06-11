<?php

namespace CodebarAg\DocuWare\Data\Users;

use CodebarAg\DocuWare\Data\DocuWareData;
use Illuminate\Support\Arr;

/**
 * A user's regional settings (language and culture).
 */
final class RegionalSettingsData extends DocuWareData
{
    public function __construct(
        public ?string $language = null,
        public ?string $culture = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            language: Arr::get($data, 'Language'),
            culture: Arr::get($data, 'Culture'),
        );
    }
}
