<?php

namespace CodebarAg\DocuWare\Resources;

use CodebarAg\DocuWare\Client\DocuWareClient;
use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Data\Documents\DocumentFieldData;
use CodebarAg\DocuWare\Data\SectionData;
use CodebarAg\DocuWare\Data\Workflow\InstanceHistoryData;
use CodebarAg\DocuWare\Data\Write\IndexFields;
use CodebarAg\DocuWare\Enums\TargetFileType;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\AddApplicationProperties;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\DeleteApplicationProperties;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\GetApplicationProperties;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\UpdateApplicationProperties;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Clip;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Staple;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Unclip;
use CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple\Unstaple;
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadDocument;
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadSection;
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadThumbnail;
use CodebarAg\DocuWare\Requests\Documents\GetDocumentPreviewRequest;
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\DeleteDocument;
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\TransferDocument;
use CodebarAg\DocuWare\Requests\Documents\Sections\DeleteSection;
use CodebarAg\DocuWare\Requests\Documents\Sections\GetAllSectionsFromADocument;
use CodebarAg\DocuWare\Requests\Documents\Sections\GetASpecificSection;
use CodebarAg\DocuWare\Requests\Documents\Sections\GetTextshot;
use CodebarAg\DocuWare\Requests\Documents\Stamps\AddDocumentAnnotations;
use CodebarAg\DocuWare\Requests\Documents\Stamps\GetDocumentAnnotations;
use CodebarAg\DocuWare\Requests\Documents\Stamps\GetStamps;
use CodebarAg\DocuWare\Requests\Documents\UpdateIndexValues\UpdateIndexValues;
use CodebarAg\DocuWare\Requests\FileCabinets\Batch\BatchDocumentsUpdateFields;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\AppendASinglePDFToADocument;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\AppendFilesToADataRecord;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\ReplaceAPDFDocumentSection;
use CodebarAg\DocuWare\Requests\Workflow\GetDocumentWorkflowHistory;
use CodebarAg\DocuWare\Resources\Search\SearchQuery;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Saloon\Data\MultipartValue;

/**
 * Documents within one file cabinet — the flagship resource. Reached via
 * `DocuWare::documents($cabinetId)`.
 *
 * @phpstan-import-type IndexEntry from IndexFields
 */
final class DocumentsResource extends Resource
{
    public function __construct(
        DocuWareClient $client,
        private readonly string $fileCabinetId,
    ) {
        parent::__construct($client);
    }

    /**
     * Start a fluent search over this cabinet.
     */
    public function search(): SearchQuery
    {
        return new SearchQuery($this->client, $this->fileCabinetId);
    }

    /**
     * Fetch a single document by id.
     */
    public function find(int|string $documentId): DocumentData
    {
        $response = $this->send(new GetASpecificDocumentFromAFileCabinet(
            $this->fileCabinetId,
            (string) $documentId,
        ));

        return DocumentData::fromDocuWare($response->json());
    }

    /**
     * Total number of documents in the cabinet (filters apply when started from search()).
     */
    public function count(): int
    {
        return $this->search()->count();
    }

    /**
     * Store a new document (data record) with optional file content and index values.
     *
     * @param  IndexFields|Collection<int, IndexEntry>|null  $indexes
     */
    public function store(
        ?string $fileContent = null,
        ?string $fileName = null,
        IndexFields|Collection|null $indexes = null,
        ?string $storeDialogId = null,
    ): DocumentData {
        $response = $this->send(new CreateDataRecord(
            $this->fileCabinetId,
            $fileContent,
            $fileName,
            $this->normalizeIndexes($indexes),
            $storeDialogId,
        ));

        return DocumentData::fromDocuWare($response->json());
    }

    /**
     * Update a document's index values. Returns the updated fields keyed by field name.
     *
     * @param  IndexFields|Collection<int, IndexEntry>  $indexes
     * @return Collection<string, DocumentFieldData>
     */
    public function update(
        int|string $documentId,
        IndexFields|Collection $indexes,
        bool $forceUpdate = false,
    ): Collection {
        $response = $this->send(new UpdateIndexValues(
            $this->fileCabinetId,
            (string) $documentId,
            $this->normalizeIndexes($indexes),
            $forceUpdate,
        ));

        return collect(JsonArrays::listOfRecords($response->json('Field')))
            ->filter(fn (array $field) => is_string(Arr::get($field, 'FieldName')) && Arr::get($field, 'FieldName') !== '')
            ->mapWithKeys(fn (array $field) => [Arr::get($field, 'FieldName') => DocumentFieldData::fromDocuWare($field)]);
    }

    /**
     * Permanently delete a document.
     */
    public function delete(int|string $documentId): void
    {
        $this->send(new DeleteDocument($this->fileCabinetId, (string) $documentId));
    }

    /**
     * Download a document's binary content.
     */
    public function download(
        int|string $documentId,
        TargetFileType $targetFileType = TargetFileType::AUTO,
        bool $keepAnnotations = false,
    ): string {
        $response = $this->send(new DownloadDocument(
            $this->fileCabinetId,
            (string) $documentId,
            $targetFileType,
            $keepAnnotations,
        ));

        return (string) $response->body();
    }

    /**
     * Get a document's annotations (stamps).
     */
    public function annotations(int|string $documentId): mixed
    {
        return $this->send(new GetDocumentAnnotations($this->fileCabinetId, $documentId))->json();
    }

    /**
     * Add annotations (stamps) to a document.
     *
     * @param  array<string, mixed>  $payload
     */
    public function annotate(int|string $documentId, array $payload): mixed
    {
        return $this->send(new AddDocumentAnnotations($this->fileCabinetId, $documentId, $payload))->json();
    }

    /**
     * List the stamps available in this cabinet.
     */
    public function stamps(): mixed
    {
        return $this->send(new GetStamps($this->fileCabinetId))->json();
    }

    /**
     * List all sections of a document.
     *
     * @return Collection<int, SectionData>
     */
    public function sections(string $documentId): Collection
    {
        return $this->mapList(
            $this->send(new GetAllSectionsFromADocument($this->fileCabinetId, $documentId)),
            'Section',
            fn (array $section) => SectionData::fromDocuWare($section),
        );
    }

    /**
     * Fetch a single section by id.
     */
    public function section(string $sectionId): SectionData
    {
        return SectionData::fromDocuWare(
            $this->send(new GetASpecificSection($this->fileCabinetId, $sectionId))->json(),
        );
    }

    /**
     * Delete a section.
     */
    public function deleteSection(string $sectionId): void
    {
        $this->send(new DeleteSection($this->fileCabinetId, $sectionId));
    }

    /**
     * Get the textshot (extracted text) of a section.
     */
    public function textshot(string $sectionId): mixed
    {
        return $this->send(new GetTextshot($this->fileCabinetId, $sectionId))->json();
    }

    /**
     * Download a section's binary content.
     */
    public function downloadSection(string $sectionId): string
    {
        return (string) $this->send(new DownloadSection($this->fileCabinetId, $sectionId))->body();
    }

    /**
     * Download a thumbnail image for a section page.
     */
    public function thumbnail(string $sectionId, int $page = 0): string
    {
        return (string) $this->send(new DownloadThumbnail($this->fileCabinetId, $sectionId, $page))->body();
    }

    /**
     * Download a document preview image.
     */
    public function preview(string $documentId): string
    {
        return (string) $this->send(new GetDocumentPreviewRequest($this->fileCabinetId, $documentId))->body();
    }

    /**
     * Get a document's application properties.
     */
    public function applicationProperties(string $documentId): mixed
    {
        return $this->send(new GetApplicationProperties($this->fileCabinetId, $documentId))->json();
    }

    /**
     * Add application properties to a document.
     *
     * @param  list<array<string, mixed>>  $properties
     */
    public function addApplicationProperties(string $documentId, array $properties): mixed
    {
        return $this->send(new AddApplicationProperties($this->fileCabinetId, $documentId, $properties))->json();
    }

    /**
     * Update a document's application properties.
     *
     * @param  list<array<string, mixed>>  $properties
     */
    public function updateApplicationProperties(string $documentId, array $properties): mixed
    {
        return $this->send(new UpdateApplicationProperties($this->fileCabinetId, $documentId, $properties))->json();
    }

    /**
     * Delete application properties from a document.
     *
     * @param  list<string>  $propertyNames
     */
    public function deleteApplicationProperties(string $documentId, array $propertyNames): mixed
    {
        return $this->send(new DeleteApplicationProperties($this->fileCabinetId, $documentId, $propertyNames))->json();
    }

    /**
     * Clip documents together.
     *
     * @param  list<array<string, mixed>>  $documents
     */
    public function clip(array $documents, bool $force = false): DocumentData
    {
        return DocumentData::fromDocuWare(
            $this->send(new Clip($this->fileCabinetId, $documents, $force))->json(),
        );
    }

    /**
     * Staple documents together.
     *
     * @param  list<array<string, mixed>>  $documents
     */
    public function staple(array $documents, bool $force = false): DocumentData
    {
        return DocumentData::fromDocuWare(
            $this->send(new Staple($this->fileCabinetId, $documents, $force))->json(),
        );
    }

    /**
     * Unclip a document.
     */
    public function unclip(string $documentId): DocumentData
    {
        return DocumentData::fromDocuWare(
            $this->send(new Unclip($this->fileCabinetId, $documentId))->json(),
        );
    }

    /**
     * Unstaple a document.
     */
    public function unstaple(string $documentId): DocumentData
    {
        return DocumentData::fromDocuWare(
            $this->send(new Unstaple($this->fileCabinetId, $documentId))->json(),
        );
    }

    /**
     * Transfer a document to another file cabinet.
     */
    public function transfer(
        string $documentId,
        string $destinationFileCabinetId,
        ?string $storeDialogId = null,
        bool $keepSource = false,
    ): bool {
        return $this->send(new TransferDocument(
            $this->fileCabinetId,
            $destinationFileCabinetId,
            $documentId,
            $storeDialogId,
            null,
            $keepSource,
        ))->successful();
    }

    /**
     * Batch-update index fields on multiple documents.
     *
     * @param  array<string, mixed>  $payload
     */
    public function batchUpdate(array $payload, ?string $contentType = null): mixed
    {
        return $this->send(new BatchDocumentsUpdateFields($this->fileCabinetId, $payload, $contentType))->json();
    }

    /**
     * Append a single PDF to an existing document.
     */
    public function appendPdf(string $documentId, ?string $fileContent, ?string $fileName): SectionData
    {
        return SectionData::fromDocuWare(
            $this->send(new AppendASinglePDFToADocument($this->fileCabinetId, $documentId, $fileContent, $fileName))->json(),
        );
    }

    /**
     * Append files to an existing data record.
     *
     * @param  Collection<int, MultipartValue>  $files
     */
    public function appendFiles(string $dataRecordId, Collection $files): DocumentData
    {
        return DocumentData::fromDocuWare(
            $this->send(new AppendFilesToADataRecord($this->fileCabinetId, $dataRecordId, $files))->json(),
        );
    }

    /**
     * Replace a PDF document section.
     */
    public function replaceSection(string $sectionId, ?string $fileContent, ?string $fileName): SectionData
    {
        return SectionData::fromDocuWare(
            $this->send(new ReplaceAPDFDocumentSection($this->fileCabinetId, $sectionId, $fileContent, $fileName))->json(),
        );
    }

    /**
     * Get the workflow history of a document.
     *
     * @return Collection<int, InstanceHistoryData>
     */
    public function workflowHistory(string $documentId): Collection
    {
        return $this->mapList(
            $this->send(new GetDocumentWorkflowHistory($this->fileCabinetId, $documentId)),
            'InstanceHistory',
            fn (array $history) => InstanceHistoryData::fromDocuWare($history),
        );
    }

    /**
     * @param  IndexFields|Collection<int, IndexEntry>|null  $indexes
     * @return Collection<int, IndexEntry>|null
     */
    private function normalizeIndexes(IndexFields|Collection|null $indexes): ?Collection
    {
        if ($indexes instanceof IndexFields) {
            return $indexes->toCollection();
        }

        return $indexes;
    }
}
