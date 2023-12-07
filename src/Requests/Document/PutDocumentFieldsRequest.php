<?php

namespace CodebarAg\DocuWare\Requests\Document;

use CodebarAg\DocuWare\DTO\DocumentIndex\PrepareDTO;
use CodebarAg\DocuWare\Exceptions\UnableToUpdateFields;
use CodebarAg\DocuWare\Responses\Document\PutDocumentFieldsResponse;
use Illuminate\Support\Collection;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class PutDocumentFieldsRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    /**
     * @throws UnableToUpdateFields
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
        protected readonly ?Collection $values = null,
        protected readonly bool $forceUpdate = false,
    ) {
    }

    public function resolveEndpoint(): string
    {
//        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/Fields';
        return 'https://webhook.site/da99a8c2-b18e-4c4a-8826-980a2df1bea6';
    }

    public function defaultBody(): array
    {
        throw_unless(count($this->values) > 0, UnableToUpdateFields::noValuesProvided());

        $content = PrepareDTO::makeContent($this->values);

//        $content = [
//            "Field" => [
//                [
//                    "FieldName" => "ITEMS",
//                    "Item" => [
//                        "\$type" => "DocumentIndexFieldTable",
//                        "Row" => [
//                            [
//                                "ColumnValue" => [
//                                    [
//                                        "FieldName" => "ITEMS_IDENTIFICATION",
//                                        "Item" => 0,
//                                        "ItemElementName" => "Decimal"
//                                    ]
//                                ]
//                            ],
//                            [
//                                "ColumnValue" => [
//                                    [
//                                        "FieldName" => "ITEMS_IDENTIFICATION",
//                                        "Item" => "test",
//                                        "ItemElementName" => "String"
//                                    ]
//                                ]
//                            ],
//                            [
//                                "ColumnValue" => [
//                                    [
//                                        "FieldName" => "ITEMS_IDENTIFICATION",
//                                        "Item" => 2,
//                                        "ItemElementName" => "Decimal"
//                                    ]
//                                ]
//                            ]
//                        ]
//                    ],
//                    "ItemElementName" => "Table"
//                ]
//            ]
//        ];

        if ($this->forceUpdate) {
            $content['ForceUpdate'] = true;
        }

        return $content;
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return PutDocumentFieldsResponse::fromResponse($response);
    }
}
