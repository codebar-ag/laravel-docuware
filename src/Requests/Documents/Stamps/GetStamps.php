<?php

namespace CodebarAg\DocuWare\Requests\Documents\Stamps;

use Saloon\Enums\Method;
use Saloon\Http\Request;

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
}
