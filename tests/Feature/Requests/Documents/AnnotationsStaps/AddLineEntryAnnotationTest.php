<?php

use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\AnnotationItem;
use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\AnnotationItemLayer;
use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\AnnotationItemLayerItem;
use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\AnnotationItemLocation;
use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\AnnotationPlacement;
use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\Annotations;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\AnnotationsStamps\AddLineEntryAnnotation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can clip 2 documents', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $path = __DIR__.'/../../../../Fixtures/files';

    cleanup($this->connector, $fileCabinetId);

    [$document, $document2] = uploadFiles($this->connector, $fileCabinetId, $path);

    $annotations = new Annotations(
        pageNumber: 0,
        sectionNumber: 0,
        annotationsPlacement: new AnnotationPlacement(
            items: collect([
                new AnnotationItem(
                    type: 'Annotation',
                    layer: collect([
                        new AnnotationItemLayer(
                            id: 1,
                            items: collect([
                                new AnnotationItemLayerItem(
                                    type: 'LineEntry',
                                    color: 'Cyan',
                                    rotation: 0,
                                    transparent: false,
                                    strokeWidth: 50,
                                    arrow: false,
                                    from: new AnnotationItemLocation(
                                        x: 100,
                                        y: 100,
                                    ),
                                    to: new AnnotationItemLocation(
                                        x: 200,
                                        y: 200,
                                    ),
                                ),
                            ]),
                        ),
                    ]),
                ),
            ]),
        ),
    );

    $stamp = $this->connector->send(new AddLineEntryAnnotation(
        $fileCabinetId,
        $document->id,
        $annotations,
    ))->dto();

    expect($stamp)->toBeInstanceOf(Collection::class)
        ->and($stamp->first())->toBeInstanceOf(Annotations::class);

    Event::assertDispatched(DocuWareResponseLog::class);
})->group('annotation')->only();
