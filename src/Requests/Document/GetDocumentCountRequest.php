<?php

namespace CodebarAg\DocuWare\Requests\Document;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetDocumentCountRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $dialogId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Query/CountExpression';
    }

    public function defaultQuery(): array
    {
        return [
            'dialogId' => $this->dialogId,
        ];
    }
}
