<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\GetDocumentsRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use Illuminate\Support\Facades\Event;
use function PHPUnit\Framework\assertCount;

it('can get all documents', function () {
    Event::fake();

    $this->connector->send(new PostDocumentRequest(
        config('laravel-docuware.tests.file_cabinet_id'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();
    $this->connector->send(new PostDocumentRequest(
        config('laravel-docuware.tests.file_cabinet_id'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $documents = $this->connector->send(new GetDocumentsRequest(
        config('laravel-docuware.tests.file_cabinet_id')
    ))->dto();

    Event::assertDispatched(DocuWareResponseLog::class);
});

it('can get all documents paginated', function () {
    Event::fake();

    for ($i = 0; $i < 4; $i++) {
        $this->connector->send(new PostDocumentRequest(
            config('laravel-docuware.tests.file_cabinet_id'),
            '::fake-file-content::',
            'example.txt'
        ))->dto();
    }

    $request = new GetDocumentsRequest(
        config('laravel-docuware.tests.file_cabinet_id')
    );

    $paginator = $this->connector->paginate($request);

    $paginator->setPerPageLimit(2);

    $documents = collect();

    foreach ($paginator as $response) {
        assertCount(2, $response->dto());

        $documents->push($response->dto());
    }

    assertCount(4, $documents->flatten());

    Event::assertDispatched(DocuWareResponseLog::class);
});
