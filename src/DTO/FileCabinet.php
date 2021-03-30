<?php

namespace codebar\DocuWare\DTO;

use Arr;

class FileCabinet
{
    public static function fromJson(array $data): self
    {
        return new static(
            $data['Id'],
            $data['Name'],
            $data['Color'],
            $data['IsBasket'],
            Arr::get($data, 'AssignedCabinetId'),
        );
    }

    public function __construct(
        public string $id,
        public string $name,
        public string $color,
        public bool $isBasket,
        public ?string $assignedCabinet,
    ) {
    }
}
