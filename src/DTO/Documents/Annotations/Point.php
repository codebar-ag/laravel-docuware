<?php

namespace CodebarAg\DocuWare\DTO\Documents\Annotations;

final class Point
{
    public function __construct(
        public int $x,
        public int $y,
    ) {}

    public static function make(int $x, int $y): self
    {
        return new self($x, $y);
    }

    /**
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'X' => $this->x,
            'Y' => $this->y,
        ];
    }
}
