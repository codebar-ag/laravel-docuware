<?php

namespace CodebarAg\DocuWare\DTO;

use Carbon\Carbon;
use CodebarAg\DocuWare\Support\ParseValue;

class DocumentField
{
    public static function fromJson(array $data): self
    {
        return new self(
            name: $data['FieldName'],
            label: $data['FieldLabel'],
            value: ParseValue::field($data),
            type: $data['ItemElementName'],
        );
    }

    public function __construct(
        public string $name,
        public string $label,
        public null | int | float | Carbon | string $value,
        public string $type,
    ) {
    }
}
