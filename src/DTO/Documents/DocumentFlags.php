<?php

namespace CodebarAg\DocuWare\DTO\Documents;

use Illuminate\Support\Arr;

/**
 * The `Flags` object of a DocuWare document.
 */
final class DocumentFlags
{
    public function __construct(
        public readonly bool $isCold,
        public readonly bool $isDBRecord,
        public readonly bool $isCheckedOut,
        public readonly bool $isCopyRightProtected,
        public readonly bool $isVoiceAvailable,
        public readonly bool $hasAppendedDocuments,
        public readonly bool $isProtected,
        public readonly bool $isDeleted,
        public readonly bool $isEmail,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        return new self(
            isCold: (bool) Arr::get($data, 'IsCold', false),
            isDBRecord: (bool) Arr::get($data, 'IsDBRecord', false),
            isCheckedOut: (bool) Arr::get($data, 'IsCheckedOut', false),
            isCopyRightProtected: (bool) Arr::get($data, 'IsCopyRightProtected', false),
            isVoiceAvailable: (bool) Arr::get($data, 'IsVoiceAvailable', false),
            hasAppendedDocuments: (bool) Arr::get($data, 'HasAppendedDocuments', false),
            isProtected: (bool) Arr::get($data, 'IsProtected', false),
            isDeleted: (bool) Arr::get($data, 'IsDeleted', false),
            isEmail: (bool) Arr::get($data, 'IsEmail', false),
        );
    }

    public static function fake(): self
    {
        return new self(false, false, false, false, false, false, false, false, false);
    }
}
