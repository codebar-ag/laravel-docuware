# General
| Request                                             | Supported |
|-----------------------------------------------------|-----------|
| Create Data Record                                  | ✅         |
| Append File(s) to a Data Record                     | ✅         |
| Upload a Single File for a Data Record              | ❌         |
| Create a Data Record & Upload File                  | ❌         |
| Create Data Record & Upload File Using Store Dialog | ❌         |
| Append a Single PDF to a Document                   | ❌         |
| Replace a PDF Document Section                      | ❌         |

### Create Data Record

#### Create Data Record
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;

$document = $connector->send(new CreateDataRecord(
    $fileCabinetId,
    null,
    null,
    collect([
        IndexTextDTO::make('DOCUMENT_LABEL', '::data-entry::'),
    ]),
))->dto();
```

#### Create Table Data Record
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateTimeDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDecimalDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexNumericDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTableDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;

$tableRows = collect([
    collect([
        IndexTextDTO::make('TEXT', 'project_1'),
        IndexNumericDTO::make('INT', 1),
        IndexDecimalDTO::make('DECIMAL', 1.1),
        IndexDateDTO::make('DATE', $now),
        IndexDateTimeDTO::make('DATETIME', $now),
    ]),
    collect([
        IndexTextDTO::make('TEXT', 'project_2'),
        IndexNumericDTO::make('INT', 2),
        IndexDecimalDTO::make('DECIMAL', 2.2),
        IndexDateDTO::make('DATE', $now),
        IndexDateTimeDTO::make('DATETIME', $now),
    ]),
]);


$document = $connector->send(new CreateDataRecord(
    $fileCabinetId,
    null,
    null,
    collect([
        IndexTableDTO::make('TABLE_NAME', $tableRows)
    ]),
))->dto();
```

#### Append File(s) To A Data Record
```php
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\AppendFilesToADataRecord;

$response = $connector->send(
    new AppendFilesToADataRecord(
        fileCabinetId: $fileCabinetId,
        dataRecordId: $document->id,
        files: collect([
            new MultipartValue(
                name: 'File[]',
                value: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-2.pdf'),
                filename: 'test-2.pdf',
            ),
            new MultipartValue(
                name: 'File[]',
                value: file_get_contents(__DIR__.'/../../../../Fixtures/files/test-3.pdf'),
                filename: 'test-3.pdf',
            ),
        ])
    )
)->dto();
```
