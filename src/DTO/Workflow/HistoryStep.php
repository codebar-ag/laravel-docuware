<?php

namespace CodebarAg\DocuWare\DTO\Workflow;

use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class HistoryStep
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromJson(array $data): self
    {
        if ($stepDateTime = Arr::get($data, 'StepDate')) {
            $stepDateTime = Str::of($stepDateTime)->after('(')->before(')');
            $stepDateTime = Carbon::createFromTimestamp($stepDateTime);
        }

        $itemRaw = Arr::get($data, 'Info.Item');
        $rows = [];
        if (is_array($itemRaw)) {
            $rows = array_is_list($itemRaw)
                ? JsonArrays::listOfRecords($itemRaw)
                : [JsonArrays::associativeRow($itemRaw)];
        }
        $infoItem = collect($rows);

        return new self(
            infoItem: $infoItem,
            stepNumber: Arr::get($data, 'StepNumber'),
            stepDate: $stepDateTime,
            activityName: Arr::get($data, 'ActivityName'),
            activityType: Arr::get($data, 'ActivityType'),
            stepType: Arr::get($data, 'StepType'),
        );
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $infoItem
     */
    public function __construct(
        public Collection $infoItem,
        public int $stepNumber,
        public Carbon $stepDate,
        public string $activityName,
        public string $activityType,
        public string $stepType,
    ) {}
}
