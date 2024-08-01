# General

| Request                       | Supported |
|-------------------------------|-----------|
| Get File Cabinet Information  | ✅         |
| Get Total Number of Documents | ✅         |

### Get File Cabinet Information
```php
use CodebarAg\DocuWare\Requests\FileCabinets\General\GetFileCabinetInformation;

$fileCabinet = $connector->send(new GetFileCabinetInformation($fileCabinetId))->dto();
```

### Get Total Number Of Documents
```php
use CodebarAg\DocuWare\Requests\FileCabinets\General\GetTotalNumberOfDocuments;

$count = $connector->send(new GetTotalNumberOfDocuments(
    $fileCabinetId,
    $dialogId
))->dto();
```
