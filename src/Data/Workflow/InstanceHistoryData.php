<?php

namespace CodebarAg\DocuWare\Data\Workflow;

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Data\Support\Field;
use CodebarAg\DocuWare\Support\JsonArrays;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class InstanceHistoryData extends DocuWareData
{
    /**
     * @param  Collection<int, HistoryStepData>|null  $historySteps
     */
    public function __construct(
        public string $id,
        public string $workflowId,
        public string $name,
        public int $version,
        public bool $workflowRequest,
        public ?Carbon $startedAt,
        public string $docId,
        public ?Collection $historySteps = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        $historySteps = null;
        $stepsRaw = Arr::get($data, 'HistorySteps');
        if (is_array($stepsRaw)) {
            $historySteps = collect(JsonArrays::listOfRecords($stepsRaw))
                ->map(fn (array $historyStep) => HistoryStepData::fromDocuWare($historyStep));
        }

        return new self(
            id: Field::string($data, 'Id', self::class),
            workflowId: (string) Arr::get($data, 'WorkflowId', ''),
            name: (string) Arr::get($data, 'Name', ''),
            version: (int) Arr::get($data, 'Version', 0),
            workflowRequest: Field::bool($data, 'WorkflowRequest'),
            startedAt: ParseValue::dateInSecondsOrNull(Field::stringOrNull($data, 'StartedAt')),
            docId: (string) Arr::get($data, 'DocId', ''),
            historySteps: $historySteps,
        );
    }
}
