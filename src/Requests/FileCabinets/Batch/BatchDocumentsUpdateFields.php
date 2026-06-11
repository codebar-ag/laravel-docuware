<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\Batch;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * POST /FileCabinets/{id}/Operations/BatchDocumentsUpdateFields
 *
 * DocuWare exposes three batch shapes on this single endpoint, each with its own media type.
 * Use the named constructors ({@see self::byId()}, {@see self::bySearch()},
 * {@see self::appendKeywords()}) which set the correct Content-Type and body, or pass a raw
 * payload + content type to the constructor.
 */
final class BatchDocumentsUpdateFields extends Request implements HasBody
{
    use HasJsonBody;

    public const CONTENT_TYPE_UPDATE = 'application/vnd.docuware.platform.batchupdateprocess+json';

    public const CONTENT_TYPE_KEYWORD = 'application/vnd.docuware.platform.batchappendkeywordvalues+json';

    protected Method $method = Method::POST;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly array $payload,
        protected readonly ?string $contentType = null,
    ) {}

    /**
     * Update index fields on documents selected by their ids (BatchUpdateDocumentsSource).
     *
     * @param  list<int>  $ids
     * @param  list<array{FieldName: string, Item: mixed}>  $fields
     */
    public static function byId(
        string $fileCabinetId,
        array $ids,
        array $fields,
        string $storeDialogId = '',
        int $batchSize = 100,
        bool $breakOnError = false,
        bool $forceUpdate = true,
    ): self {
        return new self($fileCabinetId, [
            'Source' => [
                '$type' => 'BatchUpdateDocumentsSource',
                'Id' => $ids,
            ],
            'Data' => self::data($fields, $storeDialogId, $batchSize, $breakOnError, $forceUpdate),
        ], self::CONTENT_TYPE_UPDATE);
    }

    /**
     * Update index fields on documents selected by a dialog expression (BatchUpdateDialogExpressionSource).
     *
     * @param  array<string, mixed>  $expression  e.g. ['Operation' => 'And', 'Condition' => [...]]
     * @param  list<array{FieldName: string, Item: mixed}>  $fields
     */
    public static function bySearch(
        string $fileCabinetId,
        array $expression,
        array $fields,
        string $storeDialogId = '',
        int $batchSize = 100,
        bool $breakOnError = false,
        bool $forceUpdate = true,
    ): self {
        return new self($fileCabinetId, [
            'Source' => [
                '$type' => 'BatchUpdateDialogExpressionSource',
                'Expression' => $expression,
            ],
            'Data' => self::data($fields, $storeDialogId, $batchSize, $breakOnError, $forceUpdate),
        ], self::CONTENT_TYPE_UPDATE);
    }

    /**
     * Append keyword values to a keyword field across documents (BatchAppendKeywordValues).
     *
     * @param  list<int>  $docIds
     * @param  list<string>  $keywords
     */
    public static function appendKeywords(
        string $fileCabinetId,
        array $docIds,
        array $keywords,
        string $fieldName,
        string $storeDialogId = '',
        bool $breakOnError = false,
        bool $forceUpdate = true,
    ): self {
        return new self($fileCabinetId, [
            'DocId' => $docIds,
            'Keyword' => $keywords,
            'FieldName' => $fieldName,
            'BreakOnError' => $breakOnError,
            'StoreDialogId' => $storeDialogId,
            'ForceUpdate' => $forceUpdate,
        ], self::CONTENT_TYPE_KEYWORD);
    }

    /**
     * @param  list<array{FieldName: string, Item: mixed}>  $fields
     * @return array<string, mixed>
     */
    protected static function data(array $fields, string $storeDialogId, int $batchSize, bool $breakOnError, bool $forceUpdate): array
    {
        return [
            'Field' => $fields,
            'StoreDialogId' => $storeDialogId,
            'BatchSize' => (string) $batchSize,
            'BreakOnError' => $breakOnError,
            'ForceUpdate' => $forceUpdate,
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Operations/BatchDocumentsUpdateFields';
    }

    /**
     * @return array<string, string>
     */
    public function defaultHeaders(): array
    {
        return $this->contentType !== null
            ? ['Content-Type' => $this->contentType]
            : [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->payload;
    }
}
