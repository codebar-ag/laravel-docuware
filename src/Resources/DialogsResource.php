<?php

namespace CodebarAg\DocuWare\Resources;

use CodebarAg\DocuWare\Client\DocuWareClient;
use CodebarAg\DocuWare\Data\FileCabinets\DialogData;
use CodebarAg\DocuWare\Enums\DialogType;
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetAllDialogs;
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetASpecificDialog;
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetDialogsOfASpecificType;
use Illuminate\Support\Collection;

/**
 * Dialogs of a file cabinet (search / store / result …). Reached via
 * `DocuWare::dialogs($cabinetId)`.
 */
final class DialogsResource extends Resource
{
    public function __construct(
        DocuWareClient $client,
        private readonly string $fileCabinetId,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, DialogData>
     */
    public function all(): Collection
    {
        $response = $this->send(new GetAllDialogs($this->fileCabinetId));

        return $this->mapList($response, 'Dialog', fn (array $dialog) => DialogData::fromDocuWare($dialog));
    }

    /**
     * @return Collection<int, DialogData>
     */
    public function ofType(DialogType $type): Collection
    {
        $response = $this->send(new GetDialogsOfASpecificType($this->fileCabinetId, $type));

        return $this->mapList($response, 'Dialog', fn (array $dialog) => DialogData::fromDocuWare($dialog));
    }

    public function find(string $dialogId): DialogData
    {
        $response = $this->send(new GetASpecificDialog($this->fileCabinetId, $dialogId));

        return DialogData::fromDocuWare($response->json());
    }
}
