<?php

namespace CodebarAg\DocuWare\DTO\Workflow;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class HistoryStep
{
    public static function fromJson(array $data): self
    {
        if ($stepDateTime = Arr::get($data, 'StepDate')) {
            $stepDateTime = Str::of($stepDateTime)->after('(')->before(')');
            $stepDateTime = Carbon::createFromTimestamp($stepDateTime);
        }

        return new self(
            infoItem: collect(Arr::get($data, 'Info.Item')),
            stepNumber: Arr::get($data, 'StepNumber'),
            stepDate: $stepDateTime,
            activityName: Arr::get($data, 'ActivityName'),
            activityType: Arr::get($data, 'ActivityType'),
            stepType: Arr::get($data, 'StepType'),
        );
    }

    public function __construct(
        public Collection $infoItem,
        public int $stepNumber,
        public Carbon $stepDate,
        public string $activityName,
        public string $activityType,
        public string $stepType,
    ) {}
}
