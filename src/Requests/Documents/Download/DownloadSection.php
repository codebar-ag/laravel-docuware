<?php

namespace CodebarAg\DocuWare\Requests\Documents\Download;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class DownloadSection extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $sectionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Sections/'.$this->sectionId.'/Data';
    }
}
