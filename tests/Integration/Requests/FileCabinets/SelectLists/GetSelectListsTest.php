<?php

use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;

it('can list values for a select list of a discovered field', function () {
    $dialogId = sandboxSearchDialogId($this->cabinet);
    $keywordField = sandboxFieldName($this->cabinet, 'Keyword');

    // Seed a value so the dynamic select list has something to return.
    uploadTestDocument($this->cabinet);

    $values = DocuWare::selectLists($this->cabinet)->get($dialogId, $keywordField);

    expect($values)->toBeInstanceOf(Collection::class);
});
