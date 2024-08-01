# Update Index Values
| Request                   | Supported |
|---------------------------|-----------|
| Update Index Values       | ✅         |
| Update Table Index Values | ✅         |
| Update Table Field Values | ❌         |


### Update Index Values
```php
use CodebarAg\DocuWare\Requests\Documents\UpdateIndexValues\UpdateIndexValues;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO;

$response = $connector->send(new UpdateIndexValues(
    $fileCabinetId,
    $documentId,
    collect([
        IndexTextDTO::make('DOCUMENT_LABEL', '::new-data-entry::'),
    ])
))->dto();
```

### Update Table Data Record
```php
use CodebarAg\DocuWare\Requests\Documents\UpdateIndexValues\UpdateIndexValues;
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


$document = $connector->send(new UpdateIndexValues(
    $fileCabinetId,
    null,
    null,
    collect([
        IndexTableDTO::make('TABLE_NAME', $tableRows)
    ]),
))->dto();
```
