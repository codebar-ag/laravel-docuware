<?php

namespace CodebarAg\DocuWare\Requests\General\Organization;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GetLoginToken extends Request implements Cacheable, HasBody
{
    use HasDocuWareCaching;
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  list<string>  $targetProducts
     */
    public function __construct(
        public array $targetProducts = ['PlatformService'],
        public string $usage = 'Multi',
        public string $lifetime = '1.00:00:00',
    ) {}

    public function resolveEndpoint(): string
    {
        return '/Organization/LoginToken';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'TargetProducts' => $this->targetProducts,
            'Usage' => $this->usage,
            'Lifetime' => $this->lifetime,
        ];
    }
}
