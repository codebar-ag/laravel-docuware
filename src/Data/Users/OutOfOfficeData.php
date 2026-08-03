<?php

namespace CodebarAg\DocuWare\Data\Users;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\Support\Field;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * A user's out-of-office state, including the optional start/end window.
 */
final class OutOfOfficeData extends DocuWareData
{
    public function __construct(
        public bool $isOutOfOffice,
        public ?Carbon $startDateTime,
        public bool $startDateTimeSpecified,
        public ?Carbon $endDateTime,
        public bool $endDateTimeSpecified,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        $startDateTime = Arr::get($data, 'StartDateTime');

        $timeZone = config('app.timezone', 'UTC');

        if (filled($startDateTime)) {
            $startDateTime = Str::of($startDateTime)->after('(')->before(')');
            $milliseconds = (int) (string) $startDateTime;
            $startDateTime = Carbon::createFromTimestampMs($milliseconds, $timeZone);
        }

        $endDateTime = Arr::get($data, 'EndDateTime');

        if (filled($endDateTime)) {
            $endDateTime = Str::of($endDateTime)->after('(')->before(')');
            $milliseconds = (int) (string) $endDateTime;
            $endDateTime = Carbon::createFromTimestampMs($milliseconds, $timeZone);
        }

        return new self(
            isOutOfOffice: Field::bool($data, 'IsOutOfOffice'),
            startDateTime: $startDateTime,
            startDateTimeSpecified: Field::bool($data, 'StartDateTimeSpecified'),
            endDateTime: $endDateTime,
            endDateTimeSpecified: Field::bool($data, 'EndDateTimeSpecified'),
        );
    }
}
