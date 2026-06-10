<?php

namespace CodebarAg\DocuWare\DTO\Documents\Annotations;

final class Location
{
    public function __construct(
        public int $left,
        public int $top,
        public int $width,
        public int $height,
    ) {}

    public static function make(int $left, int $top, int $width, int $height): self
    {
        return new self($left, $top, $width, $height);
    }

    /**
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'Left' => $this->left,
            'Top' => $this->top,
            'Width' => $this->width,
            'Height' => $this->height,
        ];
    }
}
