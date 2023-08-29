<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;

final class DocumentThumbnail
{
    public static function fromData(array $data): self
    {
        $mime = Arr::get($data, 'mime');
        $data = Arr::get($data, 'data');

        return new self(
            mime: $mime,
            data: $data,
            base64: 'data:'.$mime.';base64,'.base64_encode($data),
        );
    }

    public function __construct(
        public string $mime,
        public string $data,
        public string $base64,
    ) {
    }
}
