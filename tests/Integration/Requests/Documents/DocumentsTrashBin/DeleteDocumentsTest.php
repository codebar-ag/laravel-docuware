<?php

use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can delete documents in trash', function () {
    Event::fake();

    $document = DocuWare::documents($this->cabinet)->store(
        fileContent: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-1.pdf'),
        fileName: 'test-1.pdf',
    );

    DocuWare::documents($this->cabinet)->delete($document->id);

    $page = DocuWare::trash()->search(perPage: 1000);

    $ids = $page->documents
        ->map(fn (Collection $row) => $row->get('ID') ?? $row->get('Id'))
        ->filter()
        ->values()
        ->all();

    $delete = DocuWare::trash()->delete($ids);

    expect($delete->successCount)->toBe($page->total);
})->group('delete', 'trash');
