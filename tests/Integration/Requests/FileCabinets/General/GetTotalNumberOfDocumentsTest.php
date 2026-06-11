<?php

use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Sleep;

it('can get a total count of documents', function () {
    $before = DocuWare::documents($this->cabinet)->search()->count();

    expect($before)->toBeInt();

    uploadTestDocument($this->cabinet);

    Sleep::for(2)->seconds(); // Wait for the document to be indexed.

    $after = DocuWare::documents($this->cabinet)->search()->count();

    expect($after)->toBe($before + 1);
});
