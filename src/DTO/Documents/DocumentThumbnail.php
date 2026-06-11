<?php

namespace CodebarAg\DocuWare\DTO\Documents;

use Illuminate\Support\Arr;

final class DocumentThumbnail
{
    /**
     * @param  array<string, mixed>  $data
     */
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
        public readonly string $mime,
        public readonly string $data,
        public readonly string $base64,
    ) {}
}
