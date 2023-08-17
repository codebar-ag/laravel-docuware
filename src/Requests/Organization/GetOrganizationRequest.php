<?php

namespace CodebarAg\DocuWare\Requests\Organization;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetOrganizationRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public string $id,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/Organizations/'.$this->id;
    }
}
