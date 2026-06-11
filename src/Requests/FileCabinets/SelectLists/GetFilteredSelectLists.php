<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\SelectLists;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class GetFilteredSelectLists extends Request implements Cacheable, HasBody
{
    use HasDocuWareCaching;
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<string, mixed>  $dialogExpression  Postman-shaped `DialogExpression` object (Operation, Condition, …).
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $dialogId,
        protected readonly string $fieldName,
        protected readonly array $dialogExpression,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Query/SelectListExpression';
    }

    public function defaultQuery(): array
    {
        return [
            'DialogId' => $this->dialogId,
            'FieldName' => $this->fieldName,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'ValuePrefix' => '',
            'Limit' => 100,
            'Typed' => true,
            'ExcludeExternal' => true,
            'DialogExpression' => $this->dialogExpression,
        ];
    }
}
