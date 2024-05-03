<?php

namespace CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers;

use Illuminate\Support\Arr;

final class OutOfOffice
{
    public static function fromJson(array $data): self
    {
        return new self(
            isOutOfOffice: Arr::get($data, 'IsOutOfOffice'),
            startDateTimeSpecified: Arr::get($data, 'StartDateTimeSpecified'),
            endDateTimeSpecified: Arr::get($data, 'EndDateTimeSpecified'),
        );
    }

    public function __construct(
        public bool $isOutOfOffice,
        public bool $startDateTimeSpecified,
        public bool $endDateTimeSpecified,
    ) {
    }
}
