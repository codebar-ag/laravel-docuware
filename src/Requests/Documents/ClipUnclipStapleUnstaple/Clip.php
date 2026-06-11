<?php

namespace CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class Clip extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  list<array<string, mixed>>  $documents
     */
    public function __construct(
        protected readonly string $documentTrayId,
        protected readonly array $documents,
        protected readonly bool $force = false
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->documentTrayId.'/Operations/ContentMerge';
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultBody(): array
    {
        return [
            'Documents' => $this->documents,
            'Operation' => 'Clip',
            'Force' => $this->force,
        ];
    }
}
