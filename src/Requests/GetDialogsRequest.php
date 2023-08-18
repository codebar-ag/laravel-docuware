<?php

namespace CodebarAg\DocuWare\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetDialogsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Dialogs';
    }
}
