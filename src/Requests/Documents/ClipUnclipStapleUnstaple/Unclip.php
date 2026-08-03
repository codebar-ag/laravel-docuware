<?php

namespace CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class Unclip extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $documentTrayId,
        protected readonly string $documentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->documentTrayId.'/Operations/ContentDivide';
    }

    public function defaultQuery(): array
    {
        return [
            'DocId' => $this->documentId,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultBody(): array
    {
        return [
            'Operation' => 'Unclip',
        ];
    }
}
