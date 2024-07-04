<?php

use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\AnnotationItem;
use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\AnnotationItemField;
use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\AnnotationItemFieldTypedValue;
use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\AnnotationItemLocation;
use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\AnnotationPlacement;
use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\Annotations;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\AnnotationsStamps\AddStampWithPosition;
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
                    type: 'StampPlacement',
                    layer: 1,
                    field: collect([
                        new AnnotationItemField(
                            name: '<#1>',
                            typedValue: new AnnotationItemFieldTypedValue(
                                item: 'Text',
                                itemElementName: 'String',
                            ),
                            value: 'Test',
                            textAsString: 'Test',
                        ),
                    ]),
                    location: new AnnotationItemLocation(
                        x: 0.5,
                        y: 0.5,
                    ),
                    stampId: 'e1b3f6cc-ed69-4af2-afa5-6d990c0144c5',
                ),
            ]),
        ),
    );

    $stamp = $this->connector->send(new AddStampWithPosition(
        $fileCabinetId,
        $document->id,
        $annotations,
    ))->dto();

    expect($stamp)->toBeInstanceOf(Collection::class)
        ->and($stamp->first())->toBeInstanceOf(Annotations::class);

    Event::assertDispatched(DocuWareResponseLog::class);
})->group('stamp');
