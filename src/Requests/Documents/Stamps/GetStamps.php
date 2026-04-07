<?php

namespace CodebarAg\DocuWare\Requests\Documents\Stamps;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class GetStamps extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Stamps';
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $raw = $response->throw()->json('Stamp');

        return collect(JsonArrays::listOfRecords($raw));
    }
}
