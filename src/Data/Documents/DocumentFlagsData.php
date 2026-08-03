<?php

namespace CodebarAg\DocuWare\Data\Documents;

use CodebarAg\DocuWare\Data\DocuWareData;
use Illuminate\Support\Arr;

/**
 * The `Flags` object of a DocuWare document.
 */
final class DocumentFlagsData extends DocuWareData
{
    public function __construct(
        public bool $isCold,
        public bool $isDBRecord,
        public bool $isCheckedOut,
        public bool $isCopyRightProtected,
        public bool $isVoiceAvailable,
        public bool $hasAppendedDocuments,
        public bool $isProtected,
        public bool $isDeleted,
        public bool $isEmail,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
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
}
