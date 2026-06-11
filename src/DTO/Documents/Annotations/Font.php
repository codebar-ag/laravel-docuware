<?php

namespace CodebarAg\DocuWare\DTO\Documents\Annotations;

final class Font
{
    public function __construct(
        public string $fontName = 'Lucida Console',
        public bool $bold = false,
        public bool $italic = false,
        public bool $underlined = false,
        public bool $strikeThrough = false,
        public int $fontSize = 200,
        public int $spacing = 0,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
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
