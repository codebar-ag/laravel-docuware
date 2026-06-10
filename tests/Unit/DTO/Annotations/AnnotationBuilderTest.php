<?php

use CodebarAg\DocuWare\DTO\Documents\Annotations\AnnotationBuilder;
use CodebarAg\DocuWare\DTO\Documents\Annotations\DeleteEntry;
use CodebarAg\DocuWare\DTO\Documents\Annotations\LineEntry;
use CodebarAg\DocuWare\DTO\Documents\Annotations\Location;
use CodebarAg\DocuWare\DTO\Documents\Annotations\Point;
use CodebarAg\DocuWare\DTO\Documents\Annotations\PolyLineEntry;
use CodebarAg\DocuWare\DTO\Documents\Annotations\RectEntry;
use CodebarAg\DocuWare\DTO\Documents\Annotations\StampField;
use CodebarAg\DocuWare\DTO\Documents\Annotations\StampPlacement;
use CodebarAg\DocuWare\DTO\Documents\Annotations\TextEntry;

it('builds a text annotation matching the DocuWare payload shape', function () {
    $payload = AnnotationBuilder::make()
        ->addEntry(new TextEntry('Test annotation', Location::make(100, 100, 1500, 500)))
        ->toArray();

    $entry = $payload['Annotations'][0]['AnnotationsPlacement']['Items'][0];

    expect($entry['$type'])->toBe('Annotation')
        ->and($entry['Layer'][0]['Id'])->toBe('1')
        ->and($entry['Layer'][0]['Items'][0]['$type'])->toBe('TextEntry')
        ->and($entry['Layer'][0]['Items'][0]['Value'])->toBe('Test annotation')
        ->and($entry['Layer'][0]['Items'][0]['Location'])->toBe([
            'Left' => 100, 'Top' => 100, 'Width' => 1500, 'Height' => 500,
        ])
        ->and($entry['Layer'][0]['Items'][0])->not->toHaveKey('id');
});

it('marks a text update with the lowercase id key', function () {
    $payload = AnnotationBuilder::make()
        ->addEntry(new TextEntry('Updated', Location::make(1, 2, 3, 4), id: 'abc-123'))
        ->toArray();

    $item = $payload['Annotations'][0]['AnnotationsPlacement']['Items'][0]['Layer'][0]['Items'][0];

    expect($item['id'])->toBe('abc-123');
});

it('places a stamp with a position as string coordinates', function () {
    $payload = AnnotationBuilder::make()
        ->addStamp(new StampPlacement('stamp-1', location: new Point(100, 200), fields: [StampField::make('<#1>', 'september')]))
        ->toArray();

    $item = $payload['Annotations'][0]['AnnotationsPlacement']['Items'][0];

    expect($item['$type'])->toBe('StampPlacement')
        ->and($item['Location'])->toBe(['X' => '100', 'Y' => '200'])
        ->and($item['StampId'])->toBe('stamp-1')
        ->and($item['Field'][0]['Name'])->toBe('<#1>')
        ->and($item['Field'][0]['TextAsString'])->toBe('september');
});

it('omits Location for a best-position stamp', function () {
    $payload = AnnotationBuilder::make()
        ->addStamp(new StampPlacement('stamp-1'))
        ->toArray();

    expect($payload['Annotations'][0]['AnnotationsPlacement']['Items'][0])->not->toHaveKey('Location');
});

it('builds rect, line, polyline and delete entries', function () {
    $payload = AnnotationBuilder::make()
        ->addEntry(new RectEntry(Location::make(100, 600, 1500, 500)))
        ->addEntry(new LineEntry(new Point(200, 1600), new Point(1600, 1600)))
        ->addEntry(new PolyLineEntry([new Point(100, 2600), new Point(1600, 2700)]))
        ->addEntry(new DeleteEntry('05e80c80-7799-4a7b-a9c4-fc21750637cc'))
        ->toArray();

    $items = $payload['Annotations'][0]['AnnotationsPlacement']['Items'][0]['Layer'][0]['Items'];

    expect($items[0]['$type'])->toBe('RectEntry')
        ->and($items[1]['$type'])->toBe('LineEntry')
        ->and($items[1]['From'])->toBe(['X' => 200, 'Y' => 1600])
        ->and($items[2]['$type'])->toBe('PolyLineEntry')
        ->and($items[2]['Stroke']['Point'])->toHaveCount(2)
        ->and($items[3])->toBe(['$type' => 'DeleteEntry', 'Id' => '05e80c80-7799-4a7b-a9c4-fc21750637cc']);
});

it('groups entries under their layer id', function () {
    $payload = AnnotationBuilder::make()
        ->addEntry(new DeleteEntry('a'), layerId: '5')
        ->addEntry(new DeleteEntry('b'), layerId: '5')
        ->toArray();

    $layer = $payload['Annotations'][0]['AnnotationsPlacement']['Items'][0]['Layer'][0];

    expect($layer['Id'])->toBe('5')
        ->and($layer['Items'])->toHaveCount(2);
});
