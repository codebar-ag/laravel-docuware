<?php

namespace CodebarAg\DocuWare\DTO\Workflow;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class InstanceHistory
{
    public static function fromJson(array $data): self
    {
        if ($startDateTime = Arr::get($data, 'StartedAt')) {
            $startDateTime = Str::of($startDateTime)->after('(')->before(')');
            $startDateTime = Carbon::createFromTimestamp($startDateTime);
        }

        if ($historySteps = Arr::get($data, 'HistorySteps')) {
            $historySteps = collect($historySteps)->map(fn (array $historyStep) => HistoryStep::fromJson($historyStep));
        }

        return new self(
            id: Arr::get($data, 'Id'),
            workflowId: Arr::get($data, 'WorkflowId'),
            name: Arr::get($data, 'Name'),
            version: Arr::get($data, 'Version'),
            workflowRequest: Arr::get($data, 'WorkflowRequest'),
            startedAt: $startDateTime,
            docId: Arr::get($data, 'DocId'),
            historySteps: $historySteps,
        );
    }

    public function __construct(
        public string $id,
        public string $workflowId,
        public string $name,
        public int $version,
        public bool $workflowRequest,
        public Carbon $startedAt,
        public string $docId,
        public ?Collection $historySteps = null,
    ) {
    }
}
