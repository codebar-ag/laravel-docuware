<?php

namespace CodebarAg\DocuWare\Data\Workflow;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\Support\Field;
use CodebarAg\DocuWare\Support\JsonArrays;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class HistoryStepData extends DocuWareData
{
    /**
     * @param  Collection<int, array<string, mixed>>  $infoItem
     */
    public function __construct(
        public Collection $infoItem,
        public int $stepNumber,
        public ?Carbon $stepDate,
        public string $activityName,
        public string $activityType,
        public string $stepType,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
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
            stepNumber: (int) Arr::get($data, 'StepNumber', 0),
            stepDate: ParseValue::dateInSecondsOrNull(Field::stringOrNull($data, 'StepDate')),
            activityName: (string) Arr::get($data, 'ActivityName', ''),
            activityType: (string) Arr::get($data, 'ActivityType', ''),
            stepType: (string) Arr::get($data, 'StepType', ''),
        );
    }
}
