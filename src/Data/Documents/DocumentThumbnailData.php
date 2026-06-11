<?php

namespace CodebarAg\DocuWare\Data\Documents;

use CodebarAg\DocuWare\Data\DocuWareData;
use Illuminate\Support\Arr;

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
        $mime = Arr::get($data, 'mime');
        $data = Arr::get($data, 'data');

        return new self(
            mime: $mime,
            data: $data,
            base64: 'data:'.$mime.';base64,'.base64_encode($data),
        );
    }
}
