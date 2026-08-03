<?php

namespace CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin;

use Illuminate\Support\Collection;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class RestoreDocuments extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<int, string>|Collection<int, string>  $ids
     */
    public function __construct(
        protected readonly array|Collection $ids = [],
    ) {}

    public function resolveEndpoint(): string
    {
        return '/TrashBin/BatchRestore';
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultBody(): array
    {
        return [
            'Id' => $this->ids instanceof Collection ? $this->ids->toArray() : $this->ids,
        ];
    }
}
