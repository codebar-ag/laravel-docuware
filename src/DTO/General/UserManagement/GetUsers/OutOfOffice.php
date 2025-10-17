<?php

namespace CodebarAg\DocuWare\DTO\General\UserManagement\GetUsers;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class OutOfOffice
{
    public static function fromJson(array $data): self
    {
        if ($startDateTime = Arr::get($data, 'StartDateTime')) {
            $startDateTime = Str::of($startDateTime)->after('(')->before(')');
            $milliseconds = (int) (string) $startDateTime;
            $startDateTime = Carbon::createFromTimestampMs($milliseconds, 'UTC');
        }

        if ($endDateTime = Arr::get($data, 'EndDateTime')) {
            $endDateTime = Str::of($endDateTime)->after('(')->before(')');
            $milliseconds = (int) (string) $endDateTime;
            $endDateTime = Carbon::createFromTimestampMs($milliseconds, 'UTC');
        }

        return new self(
            isOutOfOffice: Arr::get($data, 'IsOutOfOffice'),
            startDateTime: $startDateTime,
            startDateTimeSpecified: Arr::get($data, 'StartDateTimeSpecified'),
            endDateTime: $endDateTime,
            endDateTimeSpecified: Arr::get($data, key: 'EndDateTimeSpecified'),
        );
    }

    public function __construct(
        public bool $isOutOfOffice,
        public ?Carbon $startDateTime,
        public bool $startDateTimeSpecified,
        public ?Carbon $endDateTime,
        public bool $endDateTimeSpecified,
    ) {}
}
