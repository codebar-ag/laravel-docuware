<?php

namespace CodebarAg\DocuWare\DTO;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class FileCabinet
{
    public static function fromJson(array $data): self
    {
        return new self(
            id: Arr::get($data, 'Id'),
            name: Arr::get($data, 'Name'),
            color: Arr::get($data, 'Color'),
            isBasket: Arr::get($data, 'IsBasket'),
            assignedCabinet: Arr::get($data, 'AssignedCabinetId'),
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

    public static function fake(
        string $id = null,
        string $name = null,
        string $color = null,
        bool $isBasket = null,
        string $assignedCabinet = null,
    ): self {
        return new self(
            id: $id ?? (string) Str::uuid(),
            name: $name ?? 'Fake File Cabinet',
            color: $color ?? Arr::random(['Red', 'Blue', 'Black', 'Green', 'Yellow']),
            isBasket: $isBasket ?? Arr::random([true, false]),
            assignedCabinet: $assignedCabinet ?? Arr::random([Str::uuid(), null]),
        );
    }
}
