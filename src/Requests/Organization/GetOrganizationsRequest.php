<?php

namespace CodebarAg\DocuWare\Requests\Organization;

use CodebarAg\DocuWare\DTO\OrganizationIndex;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetOrganizationsRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/Organizations';
    }

	public function resolveResponseClass(): string
	{
		return OrganizationIndex::class;
	}
}
