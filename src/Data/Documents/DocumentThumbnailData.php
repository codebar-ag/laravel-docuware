<?php

namespace CodebarAg\DocuWare\Data\Documents;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\Support\Field;

final class DocumentThumbnailData extends DocuWareData
{
    public function __construct(
        public string $mime,
        public string $data,
        public string $base64,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        $mime = Field::string($data, 'mime', self::class);
        $bytes = Field::string($data, 'data', self::class);

        return new self(
            mime: $mime,
            data: $bytes,
            base64: 'data:'.$mime.';base64,'.base64_encode($bytes),
        );
    }
}
