<?php

namespace CodebarAg\DocuWare\Resources;

use CodebarAg\DocuWare\Data\Documents\FieldData;
use CodebarAg\DocuWare\Data\FileCabinets\FileCabinetInformationData;
use CodebarAg\DocuWare\Data\Organization\FileCabinetData;
use CodebarAg\DocuWare\Requests\Fields\GetFieldsRequest;
use CodebarAg\DocuWare\Requests\FileCabinets\General\GetFileCabinetInformation;
use CodebarAg\DocuWare\Requests\General\Organization\GetAllFileCabinetsAndDocumentTrays;
use Illuminate\Support\Collection;

/**
 * File cabinets and document trays. Reached via `DocuWare::fileCabinets()`.
 */
final class FileCabinetsResource extends Resource
{
    /**
     * @return Collection<int, FileCabinetData>
     */
    public function all(?string $organizationId = null): Collection
    {
        $response = $this->send(new GetAllFileCabinetsAndDocumentTrays($organizationId));

        return $this->mapList($response, 'FileCabinet', fn (array $cabinet) => FileCabinetData::fromDocuWare($cabinet));
    }

    public function info(string $fileCabinetId): FileCabinetInformationData
    {
        $response = $this->send(new GetFileCabinetInformation($fileCabinetId));

        return FileCabinetInformationData::fromDocuWare($response->json());
    }

    /**
     * The index fields defined on a cabinet.
     *
     * @return Collection<int, FieldData>
     */
    public function fields(string $fileCabinetId): Collection
    {
        $response = $this->send(new GetFieldsRequest($fileCabinetId));

        return $this->mapList($response, 'Fields', fn (array $field) => FieldData::fromDocuWare($field));
    }
}
