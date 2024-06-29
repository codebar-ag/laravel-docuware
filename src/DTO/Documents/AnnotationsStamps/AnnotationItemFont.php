<?php

namespace CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps;

use Illuminate\Support\Arr;

final class AnnotationItemFont
{
    public static function fromData(array $data): self
    {
        return new self(
            fontName: Arr::get($data, 'FontName'),
            bold: Arr::get($data, 'Bold'),
            italic: Arr::get($data, 'Italic'),
            underlined: Arr::get($data, 'Underlined'),
            strikeThrough: Arr::get($data, 'StrikeThrough'),
            fontSize: Arr::get($data, 'FontSize'),
            spacing: Arr::get($data, 'Spacing'),
        );
    }

    public function __construct(
        public string $fontName,
        public bool $bold,
        public bool $italic,
        public bool $underlined,
        public bool $strikeThrough,
        public int $fontSize,
        public int $spacing
    ) {
    }

    public function values(): array
    {
        return [
            'FontName' => $this->fontName,
            'Bold' => $this->bold,
            'Italic' => $this->italic,
            'Underlined' => $this->underlined,
            'StrikeThrough' => $this->strikeThrough,
            'FontSize' => $this->fontSize,
            'Spacing' => $this->spacing,
        ];
    }
}
