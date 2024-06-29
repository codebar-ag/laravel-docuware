<?php

namespace CodebarAg\DocuWare\Requests\Documents\AnnotationsStamps;

use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\Annotations;
use CodebarAg\DocuWare\Responses\Documents\AnnotationsStamps\AddStampWithPositionResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class AddStampWithPosition extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
        protected readonly Annotations $annotations,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/Annotation';
    }

    public function defaultBody(): array
    {
        ray($this->annotations->values());

        return [
            'Annotations' => [
                $this->annotations->values(),
            ],
        ];
    }

    public function createDtoFromResponse(Response $response): Collection|Enumerable
    {
        return AddStampWithPositionResponse::fromResponse($response);
    }
}
