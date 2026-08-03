<?php

namespace CodebarAg\DocuWare\Requests\Documents\Download;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class DownloadThumbnail extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $sectionId,
        protected readonly int $page = 0,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Rendering/'.$this->sectionId.'/Thumbnail';
    }

    public function defaultQuery(): array
    {
        return [
            'page' => $this->page,
        ];
    }
}
