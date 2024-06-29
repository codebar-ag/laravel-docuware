<?php

namespace CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps;

use Illuminate\Support\Arr;

final class AnnotationItemCreated
{
    public static function fromData(array $data): self
    {
        return new self(
            user: Arr::get($data, 'User'),
            time: Arr::get($data, 'Time'),
        );
    }

    public function __construct(
        public string $user,
        public string $time,
    ) {}

    public function values(): array
    {
        return [
            'User' => $this->user,
            'Time' => $this->time,
        ];
    }
}
