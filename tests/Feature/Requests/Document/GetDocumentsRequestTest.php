<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\GetDocumentsRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use Illuminate\Support\Facades\Event;

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

//    for ($i = 0; $i < 10; $i++) {
//        $this->connector->send(new PostDocumentRequest(
//            config('laravel-docuware.tests.file_cabinet_id'),
//            '::fake-file-content::',
//            'example.txt'
//        ))->dto();
//    }

    $request = new GetDocumentsRequest(
        config('laravel-docuware.tests.file_cabinet_id')
    );

    $paginator = $this->connector->paginate($request);

    $paginator->setPerPageLimit(2);

//    $paginator->setStartPage(3);
//    $paginator->setMaxPages(3);
    $paginator->getSinglePage(3);

    foreach ($paginator->collect() as $collection) {
        ray($collection);
    }

    foreach ($paginator as $response) {
        ray($response->dto());
    }

    Event::assertDispatched(DocuWareResponseLog::class);
})->only();
