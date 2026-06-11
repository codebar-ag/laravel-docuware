<?php

use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;

it('returns a select list for a discovered dialog field', function () {
    $dialogId = sandboxSearchDialogId($this->cabinet);
    $keywordField = sandboxFieldName($this->cabinet, 'Keyword');

    $values = DocuWare::selectLists($this->cabinet)->get($dialogId, $keywordField);

    expect($values)->toBeInstanceOf(Collection::class);
});

it('returns a filtered select list using a DialogExpression', function () {
    $dialogId = sandboxSearchDialogId($this->cabinet);
    $keywordField = sandboxFieldName($this->cabinet, 'Keyword');
    $textField = sandboxFieldName($this->cabinet, 'Text');

    $dialogExpression = [
        'Operation' => 'And',
        'Condition' => [
            ['DBName' => $textField, 'Value' => ['value']],
        ],
    ];

    $values = DocuWare::selectLists($this->cabinet)->filtered($dialogId, $keywordField, $dialogExpression);

    expect($values)->toBeInstanceOf(Collection::class);
});
